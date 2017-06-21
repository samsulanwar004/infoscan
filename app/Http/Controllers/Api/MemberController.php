<?php

namespace App\Http\Controllers\Api;

use App\Mail\RegisterVerification;
use App\Services\MemberService;
use App\Services\NotificationService;
use App\Services\PointService;
use App\Transformers\MemberTransformer;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

class MemberController extends BaseApiController
{

    const EXPIRED_VERIFICATION_TOKEN_IN = 2;

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

            $mustSendVerificationEmail = false;

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
            if(! (bool)$m->is_verified) {
                $m->is_verified = 0;
                $m->verification_token = strtolower(str_random(60));
                $m->verification_expired = Carbon::now()->addDay(self::EXPIRED_VERIFICATION_TOKEN_IN);
                $mustSendVerificationEmail = true;
            }

            $m->save();
            DB::commit();


            // send email register verification
            if($mustSendVerificationEmail) {
                $this->sendVerificationEmail($m);
                $this->pushNotification();
            }

            // referral bonus point
            if ($request->has('referral_code')) {
                $referral = $request->input('referral_code');

                $memberReferrer = (new MemberService)->getMemberByReferral($referral);

                $checkReferral = (new PointService)->checkMemberReferral($m->id);

                if ($memberReferrer && $checkReferral == false) {
                    $bonus = (new PointService)->getReferralPoint();
                    if ($bonus) {
                        $referralPoint = $bonus->referral_point;
                        $referrerPoint = $bonus->referrer_point;

                        // add bonus to referral and referrer
                        (new PointService)->addBonusRefer($memberReferrer, $m, $referrerPoint, $referralPoint);

                    }
                }

            }

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
            $image = env('S3_URL') . $this->uploadImage($request, $member);
            $member->avatar = $image;
            $member->save();

            DB::commit();
            return $this->success(['avatar' => $image], 200);
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

    private function sendVerificationEmail($member)
    {
        $message = (new RegisterVerification($member))
            ->onConnection(env('INFOSCAN_QUEUE', 'sync'))
            ->onQueue(config('common.queue_list.member_register_verification_email'));

        Mail::to($member->email)->queue($message);
    }

    private function pushNotification($resend = false)
    {
        $message = config('common.notification_messages.register.verification');
        (new NotificationService($message))->setData([
            'action' => 'notification',
        ])->send();
    }
}
