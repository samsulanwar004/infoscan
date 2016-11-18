<?php

namespace App\Http\Controllers\Web;

use App\User;
use App\Merchant;
use App\MerchantUser;
use Illuminate\Http\Request;

class MerchantController extends AdminController
{
    /**
     * @var string
     */
    protected $redirectAfterSave = 'merchants';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $merchants = Merchant::paginate(50);

        return view('merchants.index', compact('merchants'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('merchants.create');
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
            'company_name' => 'required|min:3|max:200',
            'address' => 'required',
            'company_email' => 'required|email|unique:merchants,company_email',
            'company_logo' => 'mimes:jpg,jpeg,png',
            'user.email.*' => 'required|unique:users,email',
        ]);


        try {
            \DB::beginTransaction();
            $merchant = $this->createNewMerchant($request);
            $users = $this->createNewUser($request);
            $this->persistData($merchant, $users);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();

            logger($e);
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant successfully saved!');
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
        $merchant = $this->getMerchantById($id);

        $merchantUsers = MerchantUser::with('user')->where('merchant_id', '=', $id)->get();

        return view('merchants.edit', compact('merchant', 'merchantUsers'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null|int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'company_name' => 'required|min:3|max:200',
            'address' => 'required',
            'company_email' => 'required|email',
            'company_logo' => 'mimes:jpg,jpeg,png',
        ]);

        try {
            $m = $this->getMerchantById($id);
            if ($request->hasFile('company_logo') != null) {          
                \Storage::delete('public/merchants/' . $m->company_logo);
            }
            $this->createNewMerchant($request, $id);
            $this->updateUser($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant successfully updated');
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
            $m = $this->getMerchantById($id);
            $m->delete();

            if ($m->company_logo != null) {
                \Storage::delete('public/merchants/' . $m->company_logo);
            }

        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant successfully deleted!');
    }

    private function persistData($merchant, $users)
    {
        foreach ($users as $user) {
            $mu = new \App\MerchantUser;
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
    private function createNewMerchant($request, $id = null)
    {
        $memberCode = strtolower(str_random(10));

        $m = is_null($id) ? new Merchant : $this->getMerchantById($id);
        $m->merchant_code = $memberCode;
        $m->company_name = $request->input('company_name');
        $m->address = $request->input('address');
        $m->company_email = $request->input('company_email');

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = sprintf(
                "%s-%s.%s",
                $memberCode,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            $m->company_logo = $filename;
            $file->storeAs('merchants', $filename, 'public');
        }

        $m->save();

        return $m;
    }


    private function createNewUser($request)
    {
        $userList = [];
        $user = $request->input('user');
        $countUser = $this->countOfUserInput($request);

        for ($i = 0; $i <= $countUser; ++$i) {
            $u = new User;

            $name = $user['name'][$i];
            $email = $user['email'][$i];
            $password = bcrypt(strtolower(str_random(10)));

            $u->name = $name;
            $u->email = $email;
            $u->password = $password;
            $u->is_active = 1;
            $u->save();

            $userList[] = $u;
        }

        return $userList;
    }

    private function updateUser($request)
    {
        $userList = [];
        $user = $request->input('user');
        $countUser = $this->countOfUserInput($request);

        for ($i = 0; $i <= $countUser; ++$i) {
            $u = is_null($user['id'][$i]) ? new Merchant : $this->getUserById($user['id'][$i]);

            $name = $user['name'][$i];
            $email = $user['email'][$i];

            $u->name = $name;
            $u->email = $email;
            $u->is_active = 1;
            $u->save();

            $userList[] = $u;
        }

        return $userList;
    }

    private function countOfUserInput(Request $request)
    {
        $count = count($request->input('user')['name']);
        return 0 === $count ? 0 : $count - 1;
    }


    /**
     * @param $id
     *
     * @return mixed
     */

    private function getMerchantById($id)
    {
        return Merchant::where('id', '=', $id)->first();
    }

    private function getUserById($id)
    {
        return User::where('id', '=', $id)->first();
    }
}
