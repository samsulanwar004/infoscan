<?php

namespace App\Http\Controllers\Api;

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
     * @param $memberCode
     * @return \Illuminate\Http\JsonResponse
     */
    function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $m = $this->getActiveMember();

            $m->name = $request->input('name');
            $m->gender = 'female' === $request->input('gender') ? 'f' : 'm';
            $m->dob = date_format(date_create($request->input('dob')), 'Y-m-d');
            $m->monthly_expense = $request->input('monthly_expense');
            $m->person_in_house = $request->input('person_in_house');
            $m->city = $request->input('city');
            $m->occupation = $request->input('occupation');
            $m->last_education = $request->input('last_education');

            $m->save();
            DB::commit();

            return $this->success('Member successfully updated!');
        } catch (Exception $e) {
            DB::rollBack();

            $this->error($e);
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
            $this->error($e);
        }
    }
}
