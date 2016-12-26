<?php

namespace App\Http\Controllers\Web;

use App\MerchantUser;
use Illuminate\Http\Request;
use App\Merchant;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MerchantUser as MailMerchantUser;
use App\Services\MerchantService;

class MerchantUserController extends AdminController
{
    /**
     * @var string
     */
    protected $redirectAfterSave = 'merchants/user';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->isAllowed('MerchantUser.List');
        if (empty($mi = (new MerchantService)->getMerchantIdByAuth()))
        {
            $mu = $this->getMerchantUserByMerchant();
        } else {
            $mu = $this->getMerchantUserByMerchant($mi);
        }

        return view('merchants.user_index', compact('mu'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->isAllowed('MerchantUser.Create');
        return view('merchants.user_create');
    }

    /**
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->session()->flash('countOfUser', $this->countOfUserInput($request));

        $this->validate($request, [
            'user.name.*' => 'required|max:200',
            'user.email.*' => 'required|unique:users,email'
        ]);

        try {
            \DB::beginTransaction();
            $this->createNewUserMerchant($request);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();

            logger($e);
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant User successfully saved!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $merchantUsers = $this->getMerchantUserById($id);

        return view('merchants.user_edit', compact('merchantUsers'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null|int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->persistMerchantUser($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant User successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $mu = $this->getMerchantUserById($id);
            $mu->delete();
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
        return redirect($this->redirectAfterSave)->with('success', 'Merchant User successfully deleted!');
    }

    /**
     * @param $request
     * @param null $id
     *
     * @return bool
     */

    private function persistMerchantUser($request, $id = null)
    {
        $user = is_null($id) ? new User() : $this->getUserById($id);
        $user->name = $request->input('name');
        if (is_null($id)) {
            $user->email = $request->input('email');
        }
        if ('******' !== $request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->is_active = $request->has('is_active') ? 1 : 0;

        return $user->save();
    }

    private function createNewUserMerchant($request)
    {
        //$userList = [];
        $user = $request->input('user');
        $countUser = $this->countOfUserInput($request);        

        for ($i = 0; $i <= $countUser; ++$i) {
            $role = (new MerchantService)->getRoleMerchant();
            $mi = (new MerchantService)->getMerchantIdByAuth();
            $u = new User;
            $mu = new MerchantUser;
            $m = $this->getMerchantById($mi->id);
            $name = $user['name'][$i];
            $email = $user['email'][$i];
            $passwordStr = strtolower(str_random(10));
            $password = bcrypt($passwordStr);

            $u->name = $name;
            $u->email = $email;
            $u->password = $password;
            $u->is_active = 1;
            $u->save();

            $mu->merchant()->associate($m);
            $mu->user()->associate($u);

            $mu->save();
            $u->roles()->attach($role->id);
            //queue mail new user account
            Mail::to($u->email)
                ->queue(new MailMerchantUser($u, $passwordStr));
        }

        return true;
    }

    /**
     * @param $id
     *
     * @return mixed
     */

    private function getMerchantUserById($id)
    {
        return MerchantUser::with('user', 'merchant')->where('user_id', '=', $id)->first();
    }

    private function getMerchantById($id)
    {
        return Merchant::where('id', '=', $id)->first();
    }

    private function getUserById($id)
    {
        return User::where('id', '=', $id)->first();
    }

    private function getMerchant()
    {
        return Merchant::all();
    }

    private function countOfUserInput(Request $request)
    {
        $count = count($request->input('user')['name']);
        return 0 === $count ? 0 : $count - 1;
    }

    private function getMerchantUserByMerchant($mi = null)
    {
        $merchantUsers = \DB::table('users')
                            ->join('merchant_users', 'users.id', '=', 'merchant_users.user_id')
                            ->where('merchant_users.deleted_at', '=', null)
                            ->orderBy('merchant_users.id', 'DESC')  
                            ->paginate(50);
        if ($mi)
        {
            $mu = $merchantUsers->where('merchant_id', '=', $mi->id);
            return $mu;
        } else {
            return $merchantUsers;
        }
    }

}
