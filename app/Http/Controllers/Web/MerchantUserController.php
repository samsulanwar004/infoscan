<?php

namespace App\Http\Controllers\Web;

use App\MerchantUser;
use Illuminate\Http\Request;
use App\Merchant;
use App\User;

class MerchantUserController extends AdminController
{
    /**
     * @var string
     */
    protected $redirectAfterSave = 'merchants/users';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $merchantUsers = MerchantUser::with('user', 'merchant')->paginate(50);

        return view('merchants.user_index', compact('merchantUsers'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $merchants = $this->getMerchant();

        return view('merchants.user_create', compact('merchants'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user.name' => 'required|max:50',
            'user.email' => 'required|unique:users,email'
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
     * @param  int  $id
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

        $merchants = $this->getMerchant();

        return view('merchants.user_edit', compact('merchantUsers', 'merchants'));
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
     * @param  int  $id
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
        $mu = is_null($id) ? new MerchantUser : $this->getMerchantUserById($id);
        $m  = $this->getMerchantById($request->input('merchant_id'));
        $u  = $this->getUserById($request->input('user_id'));
        $mu->merchant()->associate($m);
        $mu->user()->associate($u);

        $mu->save();
    }

    private function createNewUserMerchant($request)
    {
        //$userList = [];
        $user = $request->input('user');
        $countUser = $this->countOfUserInput($request);

        for ($i = 0; $i <= $countUser; ++$i) {
            $u = new User;
            $mu = new MerchantUser;
            $m = $this->getMerchantById($user['merchant'][$i]);
            $name = $user['name'][$i];
            $email = $user['email'][$i];
            $password = bcrypt(strtolower(str_random(10)));

            $u->name = $name;
            $u->email = $email;
            $u->password = $password;
            $u->is_active = 1;
            $u->save();

            $mu->merchant()->associate($m);
            $mu->user()->associate($u);

            $mu->save();
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
        return MerchantUser::with('user', 'merchant')->where('id', '=', $id)->first();
    }

    private function getMerchantById($id)
    {
        return Merchant::where('id', '=', $id)->first();
    }

    private function getUserById($d)
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

}
