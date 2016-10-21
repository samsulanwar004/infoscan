<?php


namespace App\Http\Controllers\Web;

use App\Contracts\UriTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use UriTrait;

    public function dashboard(Request $request)
    {
        return view('dashboard');
    }
}