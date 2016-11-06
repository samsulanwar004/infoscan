<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\Request;
use Rebel\Component\Rbac\Contracts\Permission;
use Rebel\Component\Rbac\Contracts\Role;

class RoleController extends Controller
{
    /**
     * @var \Rebel\Component\Rbac\Contracts\Role
     */
    private $role;
    /**
     * @var \Rebel\Component\Rbac\Contracts\Permission
     */
    private $permission;
    /**
     * Redirect url after save the role data.
     *
     * @var string
     */
    protected $redirectAfterSave = '/users/roles';

    /**
     * RbacController constructor.
     *
     * @param \Rebel\Component\Rbac\Contracts\Role $role
     * @param \Rebel\Component\Rbac\Contracts\Permission $permission
     */
    public function __construct(Role $role, Permission $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
    }

    /**
     * Load the role list.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $roles = $this->role->with('permissions')->paginate();

        return view('rbac.role_index', compact('roles'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $role = $this->role->with('permissions')->where('id', '=', $id)->first();
        $permissions = $role->permissions;

        return view('rbac.permission_list', compact('role', 'permissions'));
    }

    /**
     * Load create new role view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $permissions = $this->getPermissions(false);

        return view('rbac.role_create', compact('permissions'));
    }

    /**
     * Store role data into database
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->persistRoles($request);
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect('/users/roles')->with('success', 'Role created!');
    }

    /**
     * Load edit role view
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $role = $this->role->with('permissions')->where('id', '=', $id)->first();
        $currentPermissions = $role->permissions;

        $permissions = $this->getPermissions();

        return view('rbac.role_edit', compact('role', 'permissions', 'currentPermissions'));
    }

    /**
     * Update the role.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->persistRoles($request, $id);
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect('/users/roles')->with('success', 'Role created!');
    }

    /**
     * Remove a role
     *
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            if ($role = $this->getRoleById($id)) {
                $role->delete();

                $role->clearPermissionOfRoleCache();
                return redirect($this->redirectAfterSave)->with('success', 'Role Deleted!');
            }

            throw new Exception('There is no Role with id: ' . $id);
        } catch (Exception $e) {
            return back()->with('errors', 'Something error when deleting process!');
        }
    }

    /**
     * @param bool $isPaginate
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    private function getPermissions($isPaginate = true)
    {
        $permissions = $this->permission->orderBy('permission_name', 'ASC');

        return $isPaginate ? $permissions->paginate() : $permissions->get();
    }

    /**
     * Persist and save the role objects.
     *
     * @param \Illuminate\Http\Request $request
     * @param null $id
     *
     * @return bool
     */
    private function persistRoles(Request $request, $id = null)
    {
        $role = is_null($id) ? $this->role : $this->getRoleById($id);
        $role->role_name = $request->input('name');
        $role->role_label = $request->input('name');
        $role->is_active = $request->has('is_active') ? 1 : 0;
        DB::beginTransaction();
        if ($role->save()) {
            $role->permissions()->sync($request->input('permissions'));

            DB::commit();

            $role->clearPermissionOfRoleCache();
            return true;
        }

        DB::rollBack();

        return false;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getRoleById($id)
    {
        return $this->role->where('id', '=', $id)->first();
    }
}
