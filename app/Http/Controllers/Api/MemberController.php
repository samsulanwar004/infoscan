<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use Exception;
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

        $backAccount = decrypt($member->bank_account);

        return $this->success([
            'member_code' => $member->member_code,
            'member_name' => $member->name,
            'bank_account' => $backAccount['bank_account'],
            'account_number' => substr($backAccount['account_number'], 0, 3) . '-xxx-xxxx',
        ]);
    }

    /**
     * Update member profile
     *
     * @param \Illuminate\Http\Request $request
     * @param $memberCode
     * @return \Illuminate\Http\JsonResponse
     */
    function update(Request $request, $memberCode)
    {
        try {
            DB::beginTransaction();
            $m = $this->getActiveMember();
            $m->name = $request->input('name');
            $m->gender = $request->input('gender');

            $m->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $this->error($e);
        }

        return $this->success('Member successfully updated!');
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
