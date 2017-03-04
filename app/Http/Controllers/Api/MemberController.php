<?php

namespace App\Http\Controllers\Api;

use App\Services\MemberService;
use App\Transformers\MemberTransformer;
use DB;
use Exception;
use Illuminate\Http\Request;
use Validator;

class MemberController extends BaseApiController
{
    /**
     * Get member profile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $member = $this->getActiveMember();

        if (!$member) {
            return $this->notFound();
        }

        return $this->success(
            fractal()->item(
                $member, new MemberTransformer
            )->toArray()
        );
    }

    /**
     * Update member profile
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function update(Request $request)
    {
        try {
            $validation = (new MemberService)->validateProfileInput($request->all());
            if ($validation->fails()) {
                return $this->error($validation->errors()->getMessages(), 200, true);
            }

            DB::beginTransaction();
            $m = $this->getActiveMember();

            $m->name = $request->input('name');
            $m->email = $request->input('email');
            $m->marital_status = $request->input('marital_status');
            $m->gender = 'female' === $request->input('gender') ? 'f' : 'm';
            $m->dob = date_format(date_create($request->input('dob')), 'Y-m-d');
            $m->monthly_expense_min = $request->input('monthly_expense_min');
            $m->monthly_expense_max = $request->input('monthly_expense_max');
            $m->person_in_house = $request->input('person_in_house');
            $m->city = $request->input('city');
            $m->province_id = $request->input('province');
            $m->occupation = $request->input('occupation');
            $m->last_education = $request->input('last_education');
            $m->bank_name = $request->input('bank_name');
            $m->bank_account_name = $request->input('bank_account_name');
            $m->bank_account_number = $request->input('bank_account_number');

            $m->save();
            DB::commit();

            return $this->success('Member successfully updated!');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->error($e);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            // validate the input
            $validation = Validator::make(['email' => $request->input('email')], [
                'email' => 'required|email',
            ]);

            if ($validation->failed()) {
                throw new Exception($validation->errors()->first());
            }

            // get the member by token
            $currentEmail = $this->getActiveMember()->email;
            $inputEmail = $request->input('email');

            // compare the email from token with the user input
            if ($inputEmail !== $currentEmail) {
                throw new Exception('Email not found!');
            }

            // send forgot password email
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function updateEmailAndName(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required'
        ]);

        if ($validation->fails()) {
            throw new Exception($validation->errors()->first());
        }

        DB::beginTransaction();
        try {

            $member = $this->getActiveMember();
            $member->email = $request->input('email');
            $member->name = $request->input('name');
            $member->save();

            DB::commit();

            return $this->success();

        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }

    }

    public function changeAvatar(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'image' => 'required|image'
        ]);

        if ($validation->fails()) {
            throw new Exception($validation->errors()->first());
        }

        DB::beginTransaction();
        try {

            $member = $this->getActiveMember();
            $image = $this->uploadImage($request, $member);
            $member->avatar = $image;
            $member->save();

            DB::commit();
            return $this->success(['avatar' => env('S3_URL') . $image], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function uploadImage(Request $request, $member = null)
    {
        if ($photo = $request->file('image')) {
            if ($member != null) {
                \Storage::disk('s3')->delete($member->avatar);
            }

            $filename = sprintf(
                "%s-%s-%s.%s",
                $member->member_code,
                date('YmdHis'),
                strtolower(str_random(10)),
                $photo->getClientOriginalExtension()
            );

            $name = $photo->storeAs('images/avatars', $filename, 's3');

            return $name;
        }

        return null;
    }
}
