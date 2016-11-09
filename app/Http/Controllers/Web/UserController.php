<?php

namespace App\Http\Controllers\Web;

use App\Contracts\ProfileTrait;
use App\User;
use DB;
use Exception;
use Illuminate\Http\Request;
use Rebel\Component\Rbac\Models\Role;

class UserController extends AdminController
{
    use ProfileTrait;

    /**
     * @var string
     */
    private $redirectAfterSave = 'users';
    /**
     * @var \Rebel\Component\Rbac\Contracts\Role
     */
    private $role;

    /**
     * UserController constructor.
     *
     * @param \Rebel\Component\Rbac\Contracts\Role|\Rebel\Component\Rbac\Models\Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userObject = new User;
        $filters = $this->uriExtractor($request, 'filter');

        if (!empty($filters)) {

            foreach ($filters as $key => $value) {
                $identifier = $userObject->filterIdentifier[$key];

                if (is_array($value)) {
                    $userObject->whereIn($identifier, $value);
                } else {
                    $userObject->where($identifier, '=', $value);
                }
            }
        }


        $users = $userObject->get();

        return view('users.index', compact('users'));
    }

    /**
     * @param $id
     */
    public function show($id)
    {
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $roles = $this->getRoles();

        return view('users.create', compact('roles'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $user = $this->getUserById($id);
        $roles = $this->getRoles();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->persistUser($request);
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'User created!');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->persistUser($request, $id);
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'User updated!');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $this->getUserById($id);
            $user->delete();
        } catch (Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'User Deleted!');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null $id
     *
     * @return bool
     */
    private function persistUser(Request $request, $id = null)
    {
        $user = is_null($id) ? new User() : $this->getUserById($id);
        $user->name = $request->input('name');
        if (is_null($id)) {
            $user->email = $request->input('email');
        }
        if ('******' !== $request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->is_active = $request->has('is_active') ? 1 : 0;

        DB::beginTransaction();
        if ($user->save()) {
            $user->roles()->attach($request->input('role'));
            DB::commit();

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
    private function getUserById($id)
    {
        return User::where('id', '=', $id)->first();
    }

    private function getRoles()
    {
        return $this->role->orderBy('role_name', 'ASC')->get();
    }
}