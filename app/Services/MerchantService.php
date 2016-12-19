<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\MerchantUser;
use App\Merchant;
use App\User;
use Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\MerchantUser as MailMerchantUser;

class MerchantService {

	public function getMerchantIdByAuth()
    {
        $mu = MerchantUser::where('user_id', '=', Auth::user()->id)->first();
        
        return $mu;
    }

    public function getAllMerchant()
    {
    	$m = Merchant::paginate();

    	return $m;
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
                is_null($id) ? $memberCode : $m->merchant_code,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            $m->company_logo = $filename;
            $file->storeAs('merchants', $filename, 'public');
        }

        $m->save();

        return $m;
    }

    public function createNewUser($request)
    {
        $userList = [];
        $user = $request->input('user');
        $countUser = $this->countOfUserInput($request);

        for ($i = 0; $i <= $countUser; ++$i) {
            $u = new User;

            $name = $user['name'][$i];
            $email = $user['email'][$i];
            $passwordStr = strtolower(str_random(10));
            $password = bcrypt($passwordStr);

            $u->name = $name;
            $u->email = $email;
            $u->password = $password;
            $u->is_active = 1;
            $u->save();

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
            $u->is_active = $users['is_active'][$i];
            $u->save();
        }

        // Create new User.
        $newUser = $request->input('newuser');
        $newUserCount = $this->countOfNewUserInput($request);
        if ($request->has('newuser')) {
            for ($i=0; $i <= $newUserCount; ++$i) {
                $passwordStr = strtolower(str_random(10));
                $password = bcrypt($passwordStr);

                $u = new User;
                $u->name = $newUser['name'][$i];
                $u->email = $newUser['email'][$i];
                $u->password = $password;
                $u->is_active = 1;
                $u->save();

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
}