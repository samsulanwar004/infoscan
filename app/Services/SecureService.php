<?php

namespace App\Services;

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

        $member = $this->memberService->member($user['email']);
        if (!$member) {
            $this->memberService
                ->setName($user['name'])
                ->setEmail($user['email'])
                ->setGender($gender)
                ->setAvatar(isset($user['avatar']) ? $user['avatar'] : '')
                ->setIsLoginBySocialMedia(true)
                ->setSocialMediaUrl(isset($user['link']) ? $user['link'] : '')
                ->setSocialMediaType($social)
                ->register($user);
        }

        return $this->memberService->profile();
    }
}
