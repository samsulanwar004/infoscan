<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rebel\Component\Setting\Contracts\Setting;

class SettingController extends AdminController
{
    const GENERAL_GROUP_NAME = 'general';

    /**
     * @var \Rebel\Component\Setting\Contracts\Setting
     */
    private $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function index()
    {
        $this->isAllowed('Settings.List');
        $settings = $this->setting->all([self::GENERAL_GROUP_NAME]);

        return view('settings.index', compact('settings'));
    }

    public function show($id)
    {
    }

    public function create()
    {
    }

    public function store(Request $request)
    {

    }

    public function edit($id)
    {
    }

    public function update(Request $request)
    {
    }

    public function destroy($id)
    {
    }
}