<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Services\TransactionService;

class SecureService
{
    /**
     * @var MemberService
     */
    private $memberService;

    public function __construct()
    {
        $this->memberService = new MemberService;
    }

    /**
     * Login or Register handler.
     *
     * @param  string $social
     * @param  array $user
     * @return mixed
     */
    public function socialHandle($social, array $user)
    {
        $gender = 'm';
        if (isset($user['gender'])) {
            $gender = 'female' === $user['gender'] ? 'f' : 'm';
        }

        $member = $this->memberService->getMemberByCode($user['member_code']);

        $hasRegistered = true;
        if (!$member) {
            $hasRegistered = false;
            $member = $this->memberService
                ->setMemberCode($user['member_code'])
                ->setName($user['name'])
                ->setEmail($user['email'])
                ->setGender($gender)
                ->setAvatar(isset($user['avatar']) ? $user['avatar'] : '')
                ->setIsLoginBySocialMedia(true)
                ->setSocialMediaUrl(isset($user['link']) ? $user['link'] : '')
                ->setSocialMediaType($social)
                ->setApiToken(str_random(60))
                ->register($user);

            // //debit for new member
            // $transaction = [
            //     'member_code' => $user['member_code'],
            //     'point' => 1000,
            // ];
            // $this->transactionDebit($transaction);
        }

        return [
            'data' => [
                'has_registered' => $hasRegistered,
                'token' => $member->api_token,
                'name' => $member->name,
                'email' => $member->email,
                'gender' => $member->gender,
                'avatar' => $member->avatar,
                'social_media_type' => $member->social_media_type,
                'social_media_id' => $member->member_code,
                'social_media_url' => $member->social_media_url,
            ],
        ];
    }

    public function registerManualHandle(Request $request)
    {
        $gender = 'm';
        if ($request->has('gender')) {
            $gender = 'female' === $request->input('gender') ? 'f' : 'm';
        }

        $member = $request->has('email') ?
            $this->getMemberByEmail($request->input('email')) :
            $this->getMemberByCode($request->input('social_media_id'));

        $hasRegistered = true;
        if (!$member) {
            $hasRegistered = false;

            // check if member code exists
            $memberCode = $this->getMemberByCode($request->input('social_media_id'));
            if ($memberCode) {
                throw new \Exception('Cannot register this member. The social media id is exists.');
            }

            $this->memberService
                ->setMemberCode($request->input('social_media_id'))
                ->setName($request->input('name'))
                ->setEmail($request->input('email', ''))
                ->setGender($gender)
                ->setAvatar($request->input('avatar', ''))
                ->setIsLoginBySocialMedia(true)
                ->setSocialMediaUrl($request->input('social_media_url'))
                ->setSocialMediaType($request->input('social_media_type'))
                ->setApiToken(str_random(60))
                ->register($request->all());

            // //debit for new member
            // $transaction = [
            //     'member_code' => $request->input('social_media_id'),
            //     'point' => 1000,
            // ];
            // $this->transactionDebit($transaction);

        }

        return [
            'data' => [
                'has_registered' => $hasRegistered,
                'token' => $this->memberService->getToken(),
            ],
        ];
    }

    public function getMemberByCode($code)
    {
        return $this->memberService->getMemberByCode($code);
    }

    public function getMemberByEmail($email)
    {
        return $this->memberService->member($email);
    }

    public function transactionDebit($transaction)
    {
        $kasir = config('common.transaction.member.cashier');
        $member = config('common.transaction.member.user');
        $data = [
            'detail_transaction' => [
                '0' => [
                    'member_code_from' => $kasir,
                    'member_code_to' => $member,
                    'amount' => $transaction['point'],
                    'detail_type' => 'db'
                ],
                '1' => [
                    'member_code_from' => $member,
                    'member_code_to' => $kasir,
                    'amount' => $transaction['point'],
                    'detail_type' => 'cr'
                ],
            ],
        ];

        (new TransactionService())->debitNewMember($transaction, $data);
    }
}
