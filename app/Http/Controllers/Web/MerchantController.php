<?php

namespace App\Http\Controllers\Web;

use App\Services\MerchantService;
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
        $this->isAllowed('Merchant.List');
        $lead = auth()->user()->id;
        $merchant = new MerchantService;
        $merchants = $this->isSuperAdministrator() ? 
                        $merchant->getAllMerchant() : 
                        $merchant->getMerchantByLead($lead);
        return view('merchants.index', compact('merchants'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->isAllowed('Merchant.Create');
        return view('merchants.create');
    }

    /**
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $merchants = new MerchantService;
        $request->session()->flash('countOfUser', $merchants->countOfUserInput($request));

        $this->validate($request, [
            'company_name' => 'required|min:3|max:200',
            'address' => 'required',
            'company_email' => 'required|email|unique:merchants,company_email',
            'company_logo' => 'mimes:jpg,jpeg,png',
            'user.name.*' => 'required|max:50',
            'user.email.*' => 'required|email|unique:users,email',
            'user.phone.*' => 'required|max:15',
            'user.position.*' => 'required|max:25'
        ]);


        try {
            \DB::beginTransaction();
            $merchant = $merchants->persisteMerchant($request);
            $users = $merchants->createNewUser($request);
            $merchants->persistData($merchant, $users);
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
        $this->isAllowed('Merchant.Update');

        $merchants = new MerchantService;
        $merchant = $merchants->getMerchantById($id);

        $merchantUsers = $merchants->getMerchantUserById($id);

        return view('merchants.edit', compact('merchant', 'merchantUsers'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null|int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $merchants = new MerchantService;
        $request->session()->flash('countOfNewUser', $merchants->countOfNewUserInput($request));

        $this->validate($request, [
            'company_name' => 'required|min:3|max:200',
            'achress' => 'required',
            'company_email' => 'required|email',
            'company_logo' => 'mimes:jpg,jpeg,png',
            'user.name.*' => 'required|max:50',
            'user.email.*' => 'email',
            'user.phone.*' => 'required|max:15',
            'user.position.*' => 'required|max:25',
            'newuser.name.*' => 'required|max:50',
            'newuser.email.*' => 'required|unique:users,email',
            'newuser.phone.*' => 'required|max:15',
            'newuser.position.*' => 'required|max:25'
        ]);

        try {
            $m = $merchants->getMerchantById($id);
            if ($request->hasFile('company_logo') != null && $m->company_logo == true) {
                \Storage::delete('public/merchants/' . $m->company_logo);
            }
            \DB::beginTransaction();
            $merchants->persisteMerchant($request, $id);
            $merchants->updateUser($request, $m->id);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();

            logger($e);
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
            $merchants = new MerchantService;
            $m = $merchants->getMerchantById($id);
            // Deleting users relation with merchant
            $merchants->deleteUserMerchant($id);

            $m->delete();

            if ($m->company_logo != null) {
                \Storage::delete('public/merchants/' . $m->company_logo);
            }

        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant successfully deleted!');
    }

    public function addSettingReports(Request $request) 
    {
        try {
            $content = json_encode($request->except(['_token']));
            $name = str_random(10);
            $createdBy = auth()->user()->id;
            \DB::beginTransaction();
            $merchants = new MerchantService;
            $merchant = $merchants->createSettingReport($name, $content, $createdBy);
            \DB::commit();
            return response()->json([
                'status' => 'ok',
                'message' => 'Success',
                'data' => ['id' => $merchant]
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            logger($e);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function editSettingReports(Request $request) 
    {
        try {
            $content = json_encode($request->except(['_token']));
            $name = str_random(10);
            $createdBy = auth()->user()->id;
            \DB::beginTransaction();
            $merchants = new MerchantService;
            $merchant = $merchants->updateSettingReport($name, $content, $createdBy);
            \DB::commit();
            return response()->json([
                'status' => 'ok',
                'message' => 'Success',
                'data' => ['id' => $merchant]
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            logger($e);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
        
}