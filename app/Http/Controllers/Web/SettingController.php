<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rebel\Component\Setting\Contracts\Setting;

class SettingController extends AdminController
{
    const GENERAL_GROUP_NAME = 'general';

    /**
     * @var string
     */
    protected $redirectAfterSave = 'settings';

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
        return view('settings.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'setting_name' => 'required|max:255',
            'setting_type' => 'required|max:255',
            'setting_value' => 'required|max:255',
        ]);

        $settingName = $request->input('setting_name');
        $name = $request->input('setting_name');
        $type = $request->input('setting_type');
        $visible = $request->input('is_visible');
        $value = $request->input('setting_value');
        $createdBy = auth('web')->user()->id;

        try {

            $isVisible = is_null($visible) ? 0 : 1;
            $setting = new \App\Setting;
            $setting->setting_group = strtolower(str_replace(' ', '_', $settingName));
            $setting->setting_name = strtolower($settingName);
            $setting->setting_display_name = $settingName;
            $setting->setting_value = $value;
            $setting->setting_type = $type;
            $setting->is_visible = $isVisible;
            $setting->created_by = $createdBy;
            $setting->save();

        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Settings successfully created!');
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