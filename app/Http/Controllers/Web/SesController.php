<?php

namespace App\Http\Controllers\Web;


use Illuminate\Http\Request;
use App\SocioEconomicStatus;
use League\Flysystem\Exception;


class SesController extends AdminController
{
    public function index()
    {
        $ses = SocioEconomicStatus::orderBy('range_start')->paginate(50);
        return view('ses.index', compact('ses'));
    }

    public function create()
    {
        return view('ses.create');
    }

    public function edit($id)
    {
        $ses = SocioEconomicStatus::where('id', $id)->first();
        return view('ses.edit', compact('ses'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|string|unique',
            'range_start' => 'required|numeric|different:range_end|min:0',
            'range_end' => 'required|numeric|min:0'
        ]);
        try {
            $input = $request->all();
            if ($input['range_start'] > $input['range_end']) {
                return back()->with('errors', 'End range must be greater than start range.');
            }
            SocioEconomicStatus::create($input);
            return redirect()->action('Web\SesController@index')->with('success', 'SES succesfully saved.');
        } catch (Exception $e) {

        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => 'required|string',
            'range_start' => 'required|numeric|different:range_end|min:0',
            'range_end' => 'required|numeric|min:0'
        ]);

        try {
            $ses = SocioEconomicStatus::where('id', $id)->first();
            $input = $request->all();
            if ($input['range_start'] > $input['range_end']) {
                return back()->with('errors', 'End range must be greater than start range.');
            }
            $ses->fill($input)->save();
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect()->action('Web\SesController@index')->with('success', 'SES succesfully updated.');
    }

    public function destroy($id)
    {
        try {
            SocioEconomicStatus::where('id', $id)->delete();
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect()->action('Web\SesController@index')->with('success', 'SES succesfully deleted.');

    }

    public function show($id)
    {

    }
}