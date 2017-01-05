<?php

namespace App\Services;

use Illuminate\Http\Request;

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
        if (!$member) {
            $this->memberService
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
        }

        return ['data' => ['token' => $this->memberService->getToken()]];
    }

    public function registerManualHandle(Request $request)
    {
        $gender = 'm';
        if ($request->has('gender')) {
            $gender = 'female' === $request->input('gender') ? 'f' : 'm';
        }

        $member = $request->has('email') ?
                        $this->getMemberByEmail($request->input('email')) :
                        $this->getMemberByCode($request->input('social_media_id'))
                    ;

        if (! $member) {
            $this->memberService
                ->setMemberCode($request->input('social_media_id'))
                ->setName($request->input('name'))
                ->setEmail($request->input('email'))
                ->setGender($gender)
                ->setAvatar($request->input('avatar'))
                ->setIsLoginBySocialMedia(true)
                ->setSocialMediaUrl($request->input('social_media_url'))
                ->setSocialMediaType($request->input('social_media_type'))
                ->setApiToken(str_random(60))
                ->setBankAccount($request->input('bank_account'))
                ->register($request->all());
        }

        return ['data' => ['token' => $this->memberService->getToken()]];
    }

    public function getMemberByCode($code)
    {
        return $this->memberService->getMemberByCode(
            $request->input('social_media_id')
        );
    }

    public function getMemberByEmail($email)
    {
        return $this->memberService->member($email);
    }
}
