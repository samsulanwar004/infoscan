<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\PointService;
use App\Referral;

class ReferralController extends AdminController
{

    protected $redirectAfterSave = 'referral';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->isAllowed('Referral.List');
        $referrals = Referral::paginate(50);

        return view('referral.index', compact('referrals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->isAllowed('Referral.Create');
        return view('referral.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'referral_point' => 'required|numeric',
            'referrer_point' => 'required|numeric',
        ]);

        try {
            (new PointService)->persistReferral($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Referral successfully created!');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->isAllowed('Referral.Update');
        $referral = (new PointService)->getReferralById($id);

        return view('referral.edit', compact('referral'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'referral_point' => 'required|numeric',
            'referrer_point' => 'required|numeric',
        ]);

        try {
            (new PointService)->persistReferral($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Referral successfully updated!');
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
            $this->isAllowed('Referral.Delete');
            $r = (new PointService)->getReferralById($id);
            $r->delete();
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Referral successfully deleted!');
    }
}
