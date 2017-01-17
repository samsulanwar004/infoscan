<?php


namespace App\Http\Controllers\Web;

use App\Contracts\UriTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\PermissionDeniedException;
class AdminController extends Controller
{
    use UriTrait;

    const SUPER_USER = 'Super Administrator';

    public function dashboard(Request $request)
    {
        return view('dashboard');
    }

    public function isAllowed($roleOrPermission)
    {
    	if(! auth()->user()->hasRole($roleOrPermission)) {
    		throw new PermissionDeniedException('You does not have access to this page!');
    	}

    	return;
    }

    public function isSuperAdministrator()
    {
        return auth()->user()->hasRole(self::SUPER_USER);
    }
}