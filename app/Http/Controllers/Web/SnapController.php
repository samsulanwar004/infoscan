<?php

namespace App\Http\Controllers\Web;

use App\Services\SnapService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Exception;
use PDOException;

class SnapController extends AdminController
{

    const NAME_ROLE = 'Crowdsource Account';

    public function index()
    {   
        $this->isAllowed('Snaps.List');
        $user = auth('web')->user();
        if ($user->roles[0]->role_name == self::NAME_ROLE) {
            $id = $user->id;
            $snaps = (new SnapService)->getAvailableSnaps();
            $snaps = $snaps->filter(function($value, $Key) use ($id) {
                        return $value->user_id == $id;
                    });
        } else {
            $snaps = (new SnapService)->getAvailableSnaps();
        } 

        $snapCategorys = config("common.snap_category");
        $snapCategoryModes = config("common.snap_category_mode");

        return view('snaps.index', compact('snaps', 'snapCategorys', 'snapCategoryModes'));
    }

    public function show(Request $request, $id)
    {
        $this->isAllowed('Snaps.Show');

        try {
            $snap = (new SnapService)->getSnapById($id);         
            if(null === $snap) {
                throw new Exception('Id Snap not valid!');
            }

            $paymentMethods = config("common.payment_methods");

            return view('snaps.show', compact('snap', 'paymentMethods'));
        } catch (Exception $e) {
            logger($e->getMessage());
            return view('errors.404');
        }           
        
    }

    public function filter($type, $mode)
    {
        $this->isAllowed('Snaps.List');
        $user = auth('web')->user();
        $snaps = ( new SnapService);

        if ($type == 'all' && $mode == 'all') {
            if ($user->roles[0]->role_name == self::NAME_ROLE) {
                $id = $user->id;
                $snaps = (new SnapService)->getAvailableSnaps();
                $snaps = $snaps->filter(function($value, $Key) use ($id) {
                            return $value->user_id == $id;
                        });
            } else {
                $snaps = (new SnapService)->getAvailableSnaps();
            }            
        } else if($type == 'all') {
            $mode = config("common.snap_mode.$mode");            
            if ($user->roles[0]->role_name == self::NAME_ROLE) {
                $id = $user->id;
                $snaps = $snaps->getSnapsByMode($mode);
                $snaps = $snaps->filter(function($value, $Key) use ($id) {
                            return $value->user_id == $id;
                        });
            } else {
                $snaps = $snaps->getSnapsByMode($mode);
            } 
        } else if($mode == 'all') {
            $type = config("common.snap_type.$type");            
            if ($user->roles[0]->role_name == self::NAME_ROLE) {
                $id = $user->id;
                $snaps = $snaps->getSnapsByType($type);
                $snaps = $snaps->filter(function($value, $Key) use ($id) {
                            return $value->user_id == $id;
                        });
            } else {
                $snaps = $snaps->getSnapsByType($type);
            }
        } else {
            $type = config("common.snap_type.$type");
            $mode = config("common.snap_mode.$mode");

            if ($user->roles[0]->role_name == self::NAME_ROLE) {
                $id = $user->id;
                $snaps = $snaps->getSnapsByFilter($type, $mode);
                $snaps = $snaps->filter(function($value, $Key) use ($id) {
                            return $value->user_id == $id;
                        });
            } else {
                $snaps = $snaps->getSnapsByFilter($type, $mode);
            }
            
        }

        return view('snaps.table_snaps', compact('snaps'));
    }

    public function edit($id)
    {
        try {
            $snap = (new SnapService)->getSnapByid($id);

            $fixedPoint = (new PointService)->calculateApprovePoint($snap);

            return view('snaps.edit', compact('snap', 'fixedPoint'));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        } 
        
    }

    public function editSnapFile($id)
    {
        try {
            $modeView = [
                'input' => 'modal_tags',
                'tags' => 'modal_tags',
                'audios' => 'modal_audios',
                'image' => 'modal_snaps',
                'no_mode' => 'modal_tags',
            ];

            $snapFile = (new SnapService)->getSnapFileById($id);

            $view = null === $snapFile->mode_type ? 'image' : $snapFile->mode_type;
            $mode = $modeView[$snapFile->mode_type];

            return view("snaps.$mode", compact('snapFile'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function snapDetail($id)
    {
        $snap = (new SnapService)->getSnapById($id);

        return view('snaps.show_detail', compact('snap'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tag.name.*' => 'required|max:255',
            'tag.qty.*' => 'required|max:11',
            'tag.total.*' => 'required|max:15',
            'newtag.name.*' => 'required|max:255',
            'newtag.qty.*' => 'required|max:11',
            'newtag.total.*' => 'required|max:15',
            'comment' => 'max:100',
            'outlet_name' => 'max:100',
            'location' => 'max:255',
            'receipt_id' => 'max:100',
            'outlet_type' => 'max:100',
            'outlet_city' => 'max:100',
            'outlet_province' => 'max:100',
            'outlet_zip_code' => 'max:100',
            'longitude' => 'max:100',
            'latitude' => 'max:100',
        ]);

        try {
            if ($request->input('mode') === 'input') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode') === 'tags') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode') === 'no_mode') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode') === 'audios') {
                (new SnapService)->updateSnapModeAudios($request, $id);
            } else if ($request->input('mode') === 'image') {
                (new SnapService)->updateSnapModeImages($request, $id);
            }else if ($request->input('mode') === 'confirm') {
                (new SnapService)->confirmSnap($request, $id);
            } else {
                $snap = (new SnapService);
                $snap->updateSnap($request, $id);
                $totalValue = $snap->getTotalValue();
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (PDOException $e) {
            return response()->json([
                'status' => 'error',                
                'message' => $e->getMessage(),
            ], 500);
        }

        $mode = ($request->has('mode') == true) ? $request->input('mode') : "";
        return response()->json([
            'status' => 'ok',
            'data' => isset($totalValue) ? clean_numeric($totalValue,'%',false,'.') : '',
            'message' => 'Snaps '.$mode.' successfully updated!',
        ]);

    }

    public function tagging($id)
    {
        $rs = (new SnapService)->getSnapFileById($id);

        return response()->json($rs->tag);
    }

}
