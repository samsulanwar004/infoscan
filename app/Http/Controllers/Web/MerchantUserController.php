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
     
        try {
            $this->persistMerchantUser($request);
        } catch (\Exception $e) {
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

}
