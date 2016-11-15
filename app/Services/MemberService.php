<?php

namespace App\Services;

use App\Exceptions\Services\MemberServiceException;
use App\Member;
use App\Transformers\MemberTransformer;
use DB;

class MemberService
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|\App\Member|\Illuminate\Database\Eloquent\Collection
     */
    protected $member = null;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var int|bool
     */
    private $isLoginBySocialMedia;

    /**
     * @var string
     */
    private $socialMediaType;

    /**
     * @var string
     */
    private $socialMediaUrl = '';

    /**
     * @var string
     */
    private $avatar = '';

    /**
     * @var string
     */
    private $status = 'active';

    /**
     * Add new a member into database.
     *
     * @param array $data
     * @param null $model
     * @return \App\Member|null
     */
    public function register(array $data, $model = null)
    {
        DB::beginTransaction();

        $member = null == $model ? $this->member() : $model;
        $member->member_code = strtolower(str_random(10));
        $member->name = $this->getName();
        $member->email = $this->getEmail();
        $member->password = $this->getPassword();
        $member->gender = $this->getGender();
        $member->avatar = $this->getAvatar();
        $member->status = $this->getStatus();
        $member->is_login_by_social_media = $this->getIsLoginBySocialMedia();
        $member->social_media_type = $this->getSocialMediaType();
        $member->social_media_url = $this->getSocialMediaUrl();

        if (!$member->save()) {
            DB::rollback();
            throw new \App\Services\MemberServiceException('Can not register member.');
        }

        DB::commit();

        return $member;
    }

    /**
     * Get member model instance
     *
     * @param null $email
     * @return \Illuminate\Database\Eloquent\Model|\App\Member|\Illuminate\Database\Eloquent\Collection
     */
    public function member($email = null)
    {
        return $this->member = null == $email ?
            new Member :
            Member::where('email', '=', $email)->first();
    }

    /**
     * @return array
     * @throws \App\Exceptions\Services\MemberServiceException
     */
    public function profile()
    {
        if (null === $this->member) {
            throw new MemberServiceException('Can not get member profile!');
        }

        return fractal()->item($this->member, new MemberTransformer)
                        ->toArray();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function setIsLoginBySocialMedia($bool = true)
    {
        $this->isLoginBySocialMedia = $bool;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsLoginBySocialMedia()
    {
        return $this->isLoginBySocialMedia;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setSocialMediaType($type)
    {
        $this->socialMediaType = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSocialMediaType()
    {
        return $this->socialMediaType;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setSocialMediaUrl($url)
    {
        $this->socialMediaUrl = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getSocialMediaUrl()
    {
        return $this->socialMediaUrl;
    }

    /**
     * @param $avatar
     * @return $this
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return $this
     */
    public function setStatus($status = null)
    {
        $this->status = null === $status ? 'active' : $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}
