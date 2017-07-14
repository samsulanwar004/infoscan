<?php

namespace App\Http\Controllers\Web;

use App\Contracts\UriTrait;
use App\Exceptions\PermissionDeniedException;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use UriTrait;

    const SUPER_USER = 'Super Administrator';

    public function dashboard(Request $request)
    {
        $reasons = Setting::select('setting_value')
            ->where('setting_group', '=', 'snap_reason')
            ->get()
            ->pluck('setting_value')
            ->toArray();
        return view('dashboard', compact('reasons'));
    }

    public function isAllowed($roleOrPermission)
    {
        if (!auth()->user()->hasRole($roleOrPermission)) {
            throw new PermissionDeniedException('You does not have access to this page!');
        }

        return;
    }

    public function isSuperAdministrator()
    {
        return auth()->user()->hasRole(self::SUPER_USER);
    }
}
