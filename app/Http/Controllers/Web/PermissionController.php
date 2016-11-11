<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Rebel\Component\Rbac\Contracts\Permission;

class PermissionController extends Controller
{

    /**
     * @var \Rebel\Component\Rbac\Contracts\Permission
     */
    private $permission;

    /**
     * PermissionController constructor.
     *
     * @param \Rebel\Component\Rbac\Contracts\Permission $permission
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $groupOfPermissions = $this->permission->distinctGroup();

        return view('rbac.permission_create', compact('groupOfPermissions'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->persistPermission($request);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Permission created!',
        ]);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $permission = $this->getPermissionById($id);
        $groupOfPermissions = $this->permission->distinctGroup();

        return view('rbac.permission_edit', compact('groupOfPermissions', $permission));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->persistPermission($request, $id);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Permission updated!',
        ]);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $p = $this->getPermissionById($id);
            if (!$p) {
                throw new Exception('There is no permission with id: ' . $id);
            }

            $p->delete();
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Permission deleted!',
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null $id
     *
     * @return bool
     */
    private function persistPermission(Request $request, $id = null)
    {
        $p = is_null($id) ? $this->permission : $this->getPermissionById($id);
        $p->permission_name = $request->input('name');
        $p->permission_label = $request->input('name');
        $p->permission_group = $request->has('is_new') ? $request->input('group_text') : $request->input('group');
        if (!$p->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function getPermissionById($id)
    {
        return $this->permission->where('id', '=', $id)->first();
    }
}