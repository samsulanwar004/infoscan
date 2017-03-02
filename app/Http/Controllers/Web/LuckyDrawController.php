<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\LuckyDrawService;

class LuckyDrawController extends AdminController
{
    /**
     * @var string
     */
    protected $redirectAfterSave = 'lucky';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $this->isAllowed('LuckyDraw.List');
        $luckys = (new LuckyDrawService)->getAllLuckyDraw();

        return view('lucky.index', compact('luckys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->isAllowed('LuckyDraw.Create');
        return view('lucky.create');
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
            'title' => 'required|max:150',
            'description' => 'required|max:255',
            'point' => 'required|max:15',
            'image' => 'mimes:jpg,jpeg,png'
        ]);

        try {
            (new LuckyDrawService)->createLuckyDraw($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Lucky draw successfully created!');
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
        $this->isAllowed('LuckyDraw.Update');
        $lucky = (new LuckyDrawService)->getLuckyDrawById($id);

        return view('lucky.edit', compact('lucky'));
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
            'title' => 'required|max:150',
            'description' => 'required|max:255',
            'point' => 'required|max:15',
            'image' => 'mimes:jpg,jpeg,png'
        ]);

        try {
            (new LuckyDrawService)->updateLuckyDraw($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage);
        }

        return redirect($this->redirectAfterSave)->with('success', 'Lucky draw successfully updated!');
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
            $l = (new LuckyDrawService)->getLuckyDrawById($id);
            $l->delete();

        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage);
        }

        return redirect($this->redirectAfterSave)->with('success', 'Lucky Draw successfully deleted!');
    }
}
