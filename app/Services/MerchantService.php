<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\MerchantUser;
use App\Merchant;
use App\User;
use App\MerchantSettingReport;
use Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\MerchantUser as MailMerchantUser;
use Rebel\Component\Rbac\Models\Role;
use App\Libraries\ImageFile;
use Image;
use Storage;

class MerchantService 
{
    const DEFAULT_FILE_DRIVER = 's3';
    const RESIZE_IMAGE = [240, null];

    /**
     * @var string
     */
    protected $s3Url;

    public function __construct()
    {
        $this->s3Url = env('S3_URL', '');
    }

	public function getMerchantIdByAuth()
    {
        $mu = MerchantUser::where('user_id', '=', Auth::user()->id)->first();

        return $mu;
    }

    public function getAllMerchant()
    {
        return Merchant::paginate();
    }

    public function getMerchantByLead($leadId)
    {
        return Merchant::join('merchant_users as mu', 'merchants.id', '=', 'mu.merchant_id')
                         ->where('mu.lead_by', '=', $leadId)
                         ->paginate();
    }

    public function getMerchantById($id)
    {
    	$m = Merchant::where('id', '=', $id)->first();

    	return $m;
    }

    public function getMerchantUserById($id)
    {
    	$mu = MerchantUser::with('user')->where('merchant_id', '=', $id)->get();

    	return $mu;
    }

    public function getSettingReportsById($id)
    {
        $sr = MerchantSettingReport::where('id', '=', $id)->first();
        return $sr;
    }

    /*public function getMerchantLeadById($id)
    {
        $mu = MerchantUser::with('user')->where('lead_by', '=', $id)->get();

        return $mu;
    }*/

    public function countOfUserInput(Request $request)
    {
        $count = count($request->input('user')['name']);
        return 0 === $count ? 0 : $count - 1;
    }

    public function countOfNewUserInput(Request $request)
    {
        $count = count($request->input('newuser')['name']);
        return 0 === $count ? 0 : $count - 1;
    }

    public function persistData($merchant, $users)
    {
        foreach ($users as $user) {
            $mu = new MerchantUser;
            $mu->merchant()->associate($merchant);
            $mu->user()->associate($user);
            $mu->lead()->associate(Auth::user()->id);

            $mu->save();
        }

        return true;
    }

    /**
     * @param $request
     * @param null $id
     *
     * @return bool
     */
    public function persisteMerchant($request, $id = null)
    {
        $memberCode = strtolower(str_random(10));

        $m = is_null($id) ? new Merchant : $this->getMerchantById($id);
        $m->merchant_code = is_null($id) ? $memberCode : $m->merchant_code;
        $m->company_name = $request->input('company_name');
        $m->address = $request->input('address');
        $m->company_email = $request->input('company_email');

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = sprintf(
                "%s-%s.%s",
                $m->merchant_code,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            //tmp file in storage local
            $path = storage_path('app/public') . "/merchants/" . $filename;
            //resize image
            $image = new ImageFile(Image::make($file->path())
                ->resize(self::RESIZE_IMAGE[0], self::RESIZE_IMAGE[1], function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
                ->putFileAs('merchants', $image, $filename, 'public');

            //delete file tmp
            Storage::delete('public/merchants/' . $filename);

            $m->company_logo = $this->completeImageLink('merchants/' . $filename);
        }

        $m->save();

        return $m;
    }

    public function createNewUser($request)
    {
        $userList = [];
        $user = $request->input('user');
        $countUser = $this->countOfUserInput($request);
        $role = $this->getRoleMerchant();

        for ($i = 0; $i <= $countUser; ++$i) {
            $u = new User;

            $name = $user['name'][$i];
            $email = $user['email'][$i];
            $phone = $user['phone'][$i];
            $position = $user['position'][$i];
            $passwordStr = strtolower(str_random(10));
            $password = bcrypt($passwordStr);

            $u->name = $name;
            $u->email = $email;
            $u->phone = $phone;
            $u->position = $position;
            $u->password = $password;
            $u->is_active = 1;
            $u->save();
            $u->roles()->attach($role->id);

            //queue mail new user account
            Mail::to($u->email)
                ->queue(new MailMerchantUser($u, $passwordStr));
            $userList[] = $u;
        }

        return $userList;
    }

    public function updateUser($request, $merchantId)
    {
        $users = $request->input('user');
        $userCount = count($users['name']);
        $ids = $users['id'];

        // Remove unnecessary user
        MerchantUser::where('merchant_id', '=', $merchantId)
                    ->whereNotIn('user_id', $ids)->delete();

        // update merchant user.
        for ($i=0; $i < $userCount; ++$i) {
            $userUpdateId = $users['id'][$i];
            $u = $this->getUserById($userUpdateId);
            $u->name = $users['name'][$i];
            $u->phone = $users['phone'][$i];
            $u->position = $users['position'][$i];
            $u->is_active = $users['is_active'][$i];
            $u->save();
        }

        // Create new User.
        $newUser = $request->input('newuser');
        $newUserCount = $this->countOfNewUserInput($request);
        $role = $this->getRoleMerchant();

        if ($request->has('newuser')) {
            for ($i=0; $i <= $newUserCount; ++$i) {
                $passwordStr = strtolower(str_random(10));
                $password = bcrypt($passwordStr);

                $u = new User;
                $u->name = $newUser['name'][$i];
                $u->email = $newUser['email'][$i];
                $u->phone = $newUser['phone'][$i];
                $u->position = $newUser['position'][$i];
                $u->password = $password;
                $u->is_active = 1;
                $u->save();
                $u->roles()->attach($role->id);

                //queue mail new user account
                Mail::to($u->email)
                    ->queue(new MailMerchantUser($u, $passwordStr));

                // add to merchant user

                $mu = MerchantUser::create(['merchant_id' => $merchantId, 'user_id' => $u->id]);
            }
        }

        $request->session()->flash('countOfNewUser', null);

        return true;
    }

    public function deleteUserMerchant($id)
    {
    	return User::join('merchant_users as mu', 'users.id', '=', 'mu.user_id')
            ->where('mu.merchant_id', $id)
            ->delete();
    }

    private function getUserById($id)
    {
        return User::where('id', '=', $id)->first();
    }

    public function getRoleMerchant()
    {
        return Role::where('role_name', '=', 'Vendor Account')->first();
    }

    public function createSettingReport($name, $content, $createdBy)
    {
        $m = new MerchantSettingReport;
        $m->name = $name;
        $m->content = $content;
        $m->created_by = $createdBy;
        $m->save();
        return $m->id;
    }

    public function updateSettingReport($id, $name, $content, $updatedBy)
    {
        $m = MerchantSettingReport::findOrFail($id);
        $m->name = $name;
        $m->content = $content;
        $m->created_by = $m->created_by;
        $m->updated_by = $updatedBy;
        $m->save();
        return $m->id;
    }

    /**
     * @param $filename
     * @return string
     */
    protected function completeImageLink($filename)
    {
        return $this->s3Url . '' . $filename;
    }
}