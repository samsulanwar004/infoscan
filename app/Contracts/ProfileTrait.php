<?php

namespace App\Contracts;

use Illuminate\Http\Request;

trait ProfileTrait
{
    public function getProfile($id)
    {
        $user = \App\User::where('id', '=', $id)->first();

        return view('users.profile', compact('user'));
    }

    public function putCredential(Request $request, $id)
    {
        try {
            $user = \App\User::where('id', '=', $id)->first();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if ($request->input('password') !== '******') {
                $user->password = bcrypt($request->input('password'));
            }

            $user->save();
        } catch (\Exception $e) {
            logger($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Credentials successfully updated!',
        ], 200);
    }
}