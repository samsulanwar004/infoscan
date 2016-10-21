<?php

namespace App\Http\Controllers\Web;

use App\User;
use Illuminate\Http\Request;

class UserController extends AdminController
{

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
        dd($users->first());
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
    }

    public function create()
    {
    }

    public function edit($id)
    {
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy(Request $request, $id)
    {
    }
}