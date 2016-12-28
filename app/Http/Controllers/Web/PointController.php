<?php

namespace App\Http\Controllers\Web;

use App\Services\PointService;
use Illuminate\Http\Request;

class PointController extends AdminController
{
    /**
     * @var string
     */
    protected $redirectAfterSave = 'points';

    public function index(Request $request)
    {
        $this->isAllowed('Points.List');
        if ($request->wantsJson()) {
            return (new PointService)->getPivotGrid();
        }

        return view('points.index');
    }

    public function show($id)
    {

    }

    public function create()
    {
        $this->isAllowed('Points.Create');
        $categories = $this->getSnapCategory();
        $levels = $this->getLevels();
        $lastLevels = (new PointService)->lastLevel();

        if ($lastLevels) {
            $arrayLevel = explode(' ', $lastLevels->name);
            $lastLevel = $arrayLevel[1];
        } else {
            $lastLevel = 0;
        }

        return view('points.create', compact('categories', 'levels', 'lastLevel'));
    }

    public function store(Request $request)
    {        

        $this->validate($request, [
            'name' => 'required|unique:tasks,name',
            'levels.*' => 'required'
        ]);

        try {
            (new PointService)->addTaskLevelPoint($request);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        } catch (\PDOException $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        } 

        return redirect($this->redirectAfterSave)->with('success', 'Task Level Points successfully created!');

    }

    public function edit($id)
    {
        $this->isAllowed('Points.Update');
        $task = (new PointService)->getTaskById($id);
        $levels = $task->levels;

        return view('points.edit', compact('task', 'levels'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'levels.*' => 'required'
        ]);

        try {
            (new PointService)->updateTaskLevelPoint($request, $id);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        } catch (\PDOException $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Task Level Points successfully updated!');

    }

    public function destroy($id)
    {

    }

    protected function getSnapCategory($key = null)
    {
        if($key) {
            return config("common.snap_category.$key");
        }

        return config('common.snap_category');
    }

    protected function getLevels()
    {
        return (new PointService)->getLevels();
    }
}
