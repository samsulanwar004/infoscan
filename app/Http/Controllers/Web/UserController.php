<?php

namespace App\Http\Controllers\Web;

use App\User;
use Illuminate\Http\Request;

class UserController extends AdminController
{

    public function index(Request $request)
    {
        $users = new User;
        // ?filter=roles:administrator,crowd-source,vendor|status:active,non-active,suspended&blabla=scrum:master,diff
        if ($request->exists('filter')) {
            $filters = $this->uriExtractor($request, 'filter');
            if (!empty($filters)) {
                foreach ()
            }
        }

        return view('users.index');
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