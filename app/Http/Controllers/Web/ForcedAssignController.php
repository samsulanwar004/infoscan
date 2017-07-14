<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\CrowdsourceService;
use App\Jobs\ForcedAssignProcessJob;

class ForcedAssignController extends AdminController
{
    protected $redirectAfterSave = 'forced-assign';

    protected $csService;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(CrowdsourceService $csService)
    {
        $this->csService = $csService;
    }

    public function index()
    {
        $this->isAllowed('Crowdsource.List');

        $crowdsources = $this->csService->getAllCrowdsource();

        $remainAssign = $this->csService->getRemainAssignSnap();

        return view('forcedassign.index', compact('crowdsources', 'remainAssign'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $crowdsources = $this->csService->getAllCrowdsource();

        $crowdsources = $crowdsources->filter(function($value) use ($id) {
            return $value->id != $id;
        });

        return view('forcedassign.edit', compact('crowdsources', 'id'));
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
        $csIds = $request->input('assign');

        $csId = [];
        $no = 1;
        foreach ($csIds as $cs) {
            $csId[$no] = $cs;
            $no++;
        }

        try {
            $snaps = $this->csService->getSnapByCrowdsourceId($id);

            $no = 1;
            $snapCs = [];
            $countCs = count($csId);
            foreach ($snaps as $snap) {
                $no = $no > $countCs ? 1 : $no;
                $cs = $csId[$no];

                $snapCs[] = [
                    'snap_id' => $snap->id,
                    'cs_id' => $cs,
                ];

                $no++;
            }

            foreach ($snapCs as $snap) {
                $config = config('common.queue_list.assign_process');
                $job = (new ForcedAssignProcessJob($snap['snap_id'], $snap['cs_id']))
                    ->onQueue($config)
                    ->onConnection(env('INFOSCAN_QUEUE'));
                dispatch($job);
            }

        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Forced assign successfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
