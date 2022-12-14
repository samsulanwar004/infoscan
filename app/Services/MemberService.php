<?php

namespace App\Services;

use App\Exceptions\Services\MemberServiceException;
use App\Member;
use App\MemberLevel;
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
     * @var string
     */
    private $maritalStatus;
    /**
     * @var string
     */
    private $memberId;


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
        $member->marital_status = $this->getMaritalStatus();
        $member->avatar = $this->getAvatar();
        $member->status = $this->getStatus();
        $member->is_login_by_social_media = $this->getIsLoginBySocialMedia();
        $member->social_media_type = $this->getSocialMediaType();
        $member->social_media_url = $this->getSocialMediaUrl();
        $member->api_token = $this->getApiToken();

        //create referral code
        $member->referral_me = unique_random('members', 'referral_me', 6);

        if (!$member->save()) {
            DB::rollback();
            throw new MemberServiceException('Can not register member.');
        }

        $this->setMemberId($member->id);

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
     * @return \Illuminate\Database\Eloquent\Collection
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
            'email' => 'required|email',
            'name' => 'required|min:3',
            'dob' => 'required|date_format:"d-m-Y"',
            'gender' => 'required|in:female,male',
            'marital_status' => 'required|in:married,single',
            'monthly_expense_min' => 'required|integer',
            'monthly_expense_max' => 'required|integer',
            'province' => 'required|min:2|max:2',
            'city' => 'required|min:3',
            //'bank_name' => 'required|min:3|max:10',
            //'bank_account_name' => 'required|min:3|max:150',
            //'bank_account_number' => 'required|min:10|max:15',
        ]);
    }

    public function getToken()
    {
        if (null === $this->member) {
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

    /**
     * Get latest point member by member id
     *
     * @param int
     * @return $latestPoint
     */
    public function getLatestPointMemberById($id)
    {
        $member = $this->getMemberById($id);
        $memberCode = $member->member_code;
        $cashier = config('common.transaction.member.cashier');
        $refund = config('common.transaction.transaction_type.refund');
        $latestPoint = \DB::table('transactions')
            ->join('transaction_detail', 'transactions.id', '=', 'transaction_detail.transaction_id')
            ->where('transactions.transaction_type', '!=', $refund)
            ->where('transaction_detail.member_code_to', '=', $memberCode)
            ->where('transaction_detail.member_code_from', '=', $cashier)
            ->where('transaction_detail.detail_type', '=', 'cr')
            ->sum('transaction_detail.amount');

        return $latestPoint;
    }

    /**
     * Get level id by member id
     *
     * @param int $id
     * @return $levelId
     */
    public function getLevelIdByMemberId($id)
    {
        $latestPoint = $this->getLatestPointMemberById($id);
        $level = \DB::select('select max(id) as level_id from level_points where ' . $latestPoint . ' - point >= 0');

        $firtLevel = \DB::select('select id from level_points limit 1');

        return ($level[0]->level_id == true) ? $level[0]->level_id : $firtLevel[0]->id;
    }

    /**
     * Get member by id
     *
     * @param int $id
     * @return Member
     */
    public function getMemberById($id)
    {
        return Member::where('id', $id)->first();
    }

    /**
     * @return string
     */
    public function getMaritalStatus()
    {
        return $this->maritalStatus;
    }

    /**
     * @param string $maritalStatus
     */
    public function setMaritalStatus($maritalStatus)
    {
        $this->maritalStatus = $maritalStatus;

        return $this;
    }

    public function getLevelByMemberId($id)
    {
        $latestPoint = $this->getLatestPointMemberById($id);
        $level = \DB::select('select max(id) as level_id from level_points where ' . $latestPoint . ' - point >= 0');

        $firtLevel = \DB::select('select id from level_points limit 1');

        $data = [
            'level_id' => ($level[0]->level_id == true) ? $level[0]->level_id : $firtLevel[0]->id,
            'latest_point' => $latestPoint,
        ];

        return $data;
    }

    private function setMemberId($value)
    {
        $this->memberId = $value;

        return $this;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function getLeaderboard()
    {
        return collect(\DB::select('SELECT id, email, name, IFNULL(leaderboard_score, 0) AS score, @curRank := @curRank + 1 AS rank FROM members m,
                (SELECT @curRank := 0) r ORDER BY leaderboard_score DESC'));
    }

    public function getLeaderboardByDinamisPoint()
    {
        return collect(\DB::select('SELECT id, name, IFNULL(temporary_point, 0) AS score, @curRank := @curRank + 1 AS rank FROM members m,
                (SELECT @curRank := 0) r ORDER BY temporary_point DESC'));
    }

    public function getLatestMemberLevelById($id)
    {
        return MemberLevel::where('member_id', $id)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getMemberByReferral($referral)
    {
        return Member::where('referral_me', '=', $referral)
            ->first();
    }
}
