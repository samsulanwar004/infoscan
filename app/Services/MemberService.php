<?php

namespace App\Services;

use App\Exceptions\Services\MemberServiceException;
use App\Member;
use App\Transformers\MemberTransformer;
use DB;
use Illuminate\Support\Facades\Validator;

class MemberService
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|\App\Member|\Illuminate\Database\Eloquent\Collection
     */
    protected $member = null;

    /**
     * @var string
     */
    private $memberCode;

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
     * @var string
     */
    private $apiToken;
    /**
     * @var string
     */
    private $bankName;

    /**
     * @var string
     */
    private $bankAccountName;
    /**
     * @var string
     */
    private $bankAccountNumber;

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
        $member->member_code = $this->getMemberCode();
        $member->name = $this->getName();
        $member->email = $this->getEmail();
        $member->password = $this->getPassword();
        $member->gender = $this->getGender();
        $member->avatar = $this->getAvatar();
        $member->status = $this->getStatus();
        $member->is_login_by_social_media = $this->getIsLoginBySocialMedia();
        $member->social_media_type = $this->getSocialMediaType();
        $member->social_media_url = $this->getSocialMediaUrl();
        $member->api_token = $this->getApiToken();

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
     * Get registered member by member code.
     *
     * @param  string $memberCode
     * @return \Illuminate\Database\Collection
     */
    public function getMemberByCode($memberCode)
    {
        return $this->member = Member::where('member_code', $memberCode)->first();
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

    public function validateProfileInput(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|min:3',
            'dob' => 'required|date_format:"d-m-Y"',
            'gender' => 'required|in:female,male',
            'monthly_expense' => 'required|integer',
            'bank_name' => 'required|min:3|max:10',
            'bank_account_name' => 'required|min:3|max:150',
            'bank_account_number' => 'required|min:10|max:15',
        ]);
    }

    public function getToken()
    {
        if(null === $this->member) {
            throw new MemberServiceException('Can not get Token');
        }

        return $this->member->api_token;
    }

    /**
     * @return mixed|string
     */
    public function getMemberCode()
    {
        return $this->memberCode;
    }

    /**
     * @param string $memberCode
     * @return $this
     */
    public function setMemberCode($memberCode)
    {
        $this->memberCode = $memberCode;

        return $this;
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

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param string
     * @return $this
     */
    public function setApiToken($token)
    {
        $this->apiToken = strtolower(trim($token));

        return $this;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param string
     * @return $this
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankAccountName()
    {
        return $this->bankAccountName;
    }

    /**
     * @param string
     * @return $this
     */
    public function setBankAccountName($bAccName)
    {
        $this->bankAccountName = $bAccName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankAccountNumber()
    {
        return $this->bankAccountNumber;
    }

    /**
     * @param string
     * @return $this
     */
    public function setBankAccountNumber($bAccNumber)
    {
        $this->bankAccountNumber = $bAccNumber;

        return $this;
    }

}
