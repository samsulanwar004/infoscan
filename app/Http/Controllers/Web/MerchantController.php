<?php

namespace App\Http\Controllers\Web;

use App\Merchant;
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
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'company_name'  => 'required|min:3|max:200',
            'address'       => 'required',
            'company_email' => 'required|email|unique:merchants,company_email',
            'company_logo'  => 'mimes:jpg,jpeg,png'
        ]);

        try {
            $this->persistMerchant($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant successfully saved!');
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
        $merchant = $this->getMerchantById($id);

        return view('merchants.edit', compact('merchant'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null|int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id = null)
    {
        try {
            $this->persistMerchant($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant successfully updated');
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
            $m = $this->getMerchantById($id);
            $m->delete();

            if ($m->company_logo != null) {
                \Storage::delete('public/' . $m->company_logo);
            }

        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Merchant successfully deleted!');
    }

    /**
     * @param $request
     * @param null $id
     *
     * @return bool
     */
    private function persistMerchant($request, $id = null)
    {
        $memberCode = strtolower(str_random(10));

        //$file             = $request->file('company_logo');
        $m                = is_null($id) ? new Merchant : $this->getMerchantById($id);
        $m->merchant_code = $memberCode;
        $m->company_name  = $request->input('company_name');
        $m->address       = $request->input('address');
        $m->company_email = $request->input('company_email');

        
        //dd($request->all(), $request->hasFile('company_logo'));
        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo'); 
            $filename = sprintf(
                "%s-%s.%s", 
                $memberCode,
                date('Ymdhis'),  
                $file->getClientOriginalExtension()
            );

            $m->company_logo = $file->storeAs(
                'merchants', $filename, 'public'
            );            
        }

        return $m->save();
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
}
