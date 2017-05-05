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

    public function index()
    {
        $this->isAllowed('Points.List');

        return view('points.index');
    }

    public function getTaskTable(Request $request)
    {
        if ($request->wantsJson()) {
            //return (new PointService)->getPivotGrid();
            return (new PointService)->getNewPivotGrid();
        }
    }

    public function getTaskLimit(Request $request)
    {
        if ($request->wantsJson()) {
            return (new PointService)->getTaskPivotLimit();
        }
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
        if ($request->input('task_type') == 'a') {
            $this->validate($request, [
                'name' => 'required|unique:tasks,name',
                'range_start' => 'required|numeric|different:range_end|min:0',
                'range_end' => 'required|numeric|min:0',
                'point' => 'required'
            ]);
        } else {
            $this->validate($request, [
                'name' => 'required|unique:tasks,name',
                'point' => 'required'
            ]);
        }

        try {

            if ($request->input('task_mode') == '0') {
                throw new \Exception("Task Mode Required");                
            }
            if ($request->input('task_type') == 'a') {
                if ($request->input('range_start') > $request->input('range_end')) {
                    throw new \Exception("End range must be greater than start range.");
                }
            }            
            
            //(new PointService)->addTaskLevelPoint($request);
            //new logic task point
            (new PointService)->addTaskPoint($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (\PDOException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } 

        // return response()->json([
        //     'status' => 'ok',
        //     'message' => 'Task Level Points successfully created!',
        // ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Task Points successfully created!',
        ]);

    }

    public function edit($id)
    {
        $this->isAllowed('Points.Update');
        $task = (new PointService)->getTaskById($id);
        $point = $task->point()->first();
        $limits = $task->limit;

        $lim = [];
        foreach ($limits as $limit) {
            $lim[$limit->name] = $limit->limit;
        }

        return view('points.edit', compact('task', 'point', 'lim'));
    }

    public function update(Request $request, $id)
    {
        if ($request->input('task_type') == 'a') {
            $this->validate($request, [
                'name' => 'required',
                'range_start' => 'required|numeric|different:range_end|min:0',
                'range_end' => 'required|numeric|min:0',
                'point' => 'required'
            ]);
        } else {
            $this->validate($request, [
                'name' => 'required',
                'point' => 'required'
            ]);
        }

        try {

            if ($request->input('task_mode') == '0') {
                throw new \Exception("Task Mode Required");                
            }

            // (new PointService)->updateTaskLevelPoint($request, $id);
            (new PointService)->updateTaskPoint($request, $id);
        } catch (\Exception $e) {
             return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (\PDOException $e) {
             return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Task Level Points successfully updated!',
        ]);
    }

    public function destroy($id)
    {
        try {
            $task = (new PointService)->getTaskById($id);
            $task->delete();
            cache()->forget('point.pivot');
            cache()->forget('point.limit');
        } catch (\Exception $e) {
            return redirect($this->redirectAfterSave)->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Task point successfully deleted!');
    }

    public function pointManager()
    {
        $this->isAllowed('Points.Create');
        $levels = $this->getLevels();
        $lastLevels = (new PointService)->lastLevel();

        if ($lastLevels) {
            $arrayLevel = explode(' ', $lastLevels->name);
            $lastLevel = $arrayLevel[1];
        } else {
            $lastLevel = 0;
        }

        return view('points.manager_points', compact('levels', 'lastLevel'));
    }

    public function pointManagerUpdate(Request $request)
    {
        $this->validate($request, [
            'levels.*' => 'required',
        ]);
        
        try {
            (new PointService)->updatePointManager($request);
        } catch (\Exception $e) {
             return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (\PDOException $e) {
             return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Point Manager successfully updated!',
        ]);
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
