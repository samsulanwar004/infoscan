<?php

namespace Rebel\Component\Setting\Supports;

use Illuminate\Http\Request;
use Cache;
use Rebel\Component\Setting\SettingException;

/**
 * Class SettingCollectionTrait
 *
 * @package Rebel\Component\Setting\Supports
 * @property $settingGroup
 * @property $settingName
 *
 * @author  Pieter Lelaona <pieter.lelaona@gmail.com>
 */
trait SettingCollectionTrait
{

    /**
     * @var int
     */
    private $cacheTTL = 60;


    /**
     * Get single / multiple setting
     *
     * @param string $group
     * @param null|string $name
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws \Rebel\Component\Setting\SettingException
     */
    protected function getSettings($group, $name = null)
    {
        // if setting cache != null then return the existing cache!
        if ($cache = $this->getSettingCache()) {

            foreach ($cache as $c) {
                if (strtolower($c->setting_group) === strtolower($group)) {
                    if ($name && strtolower($name) === strtolower($c->setting_name)) {
                        return $c;
                    }

                    return $c;
                }
            }
        }

        return $this->getDataFromDB($group, $name);
    }

    protected function getDataFromDB($group, $name = null)
    {
        $mod = $this->getSettingModel();
        if (!$group) {
            throw new SettingException('First argument (group) not set!');
        }

        $mod = $mod->where('group_name', '=', $group);

        // if name argument is set, then return single row of setting collection.
        if ($name) {
            $mod = $mod->where('setting_name', '=', $name);
            $result = $mod->first();
            $this->warmupSettingCache($result);

            return $result;
        }

        $result = $mod->get();

        $this->warmupSettingCache($result);

        return $result;
    }

    /**
     * Store a new setting into database.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool|mixed|\Illuminate\Database\Eloquent\Model
     */
    protected function storeSetting(Request $request)
    {
        $s = $this->getSettingModel();
        $s->setting_group = $this->prepareSettingGroupInput();
        $s->setting_name = $this->prepareSettingNameInput($request);
        $s->setting_display_name = $this->prepareDisplayNameInput($request);
        $s->setting_value = $this->prepareSettingValueInput($request);

        if ($s->save()) {
            $this->updateSettingCache();

            return $s;
        }


        return false;
    }

    /**
     * Update setting by given id. The setting_name still same with previous value.
     * We'll not update/replace the setting_name with the new value!
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return bool|mixed|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    protected function updateSetting(Request $request, $id)
    {
        $s = $this->getSettingById($id);
        if (!$s) {
            throw new \Exception('Setting with id: ' . $id . ' not found in database!');
        }

        $s->setting_display_name = $this->prepareDisplayNameInput($request);
        $s->setting_value = $this->prepareSettingValueInput($request);
        if ($s->save()) {
            $this->updateSettingCache();

            return $s;
        }

        return false;
    }

    /**
     * Delete a setting by given id.
     * This method used soft deletes concepts.
     *
     * @param $id
     *
     * @return mixed|bool
     */
    protected function deleteSetting($id)
    {
        $s = $this->getSettingById($id);

        return $s->delete();
    }

    /**
     * @param $id
     *
     * @return mixed|\Illuminate\Database\Eloquent\Collection
     */
    protected function getSettingById($id)
    {
        return $this->getSettingModel()->where('id', '=', $id)->first();
    }

    protected function getSettingByName($name, $singleRow = true)
    {
        $data = $this->getSettingModel()->where('setting_name', '=', $name);

        return $singleRow ? $data->first() : $data->get();
    }

    protected function getSettingByGroup($group, $singleRow = true)
    {
        $data = $this->getSettingModel()->where('setting_group', '=', $group);

        return $singleRow ? $data->first() : $data->get();
    }

    /**
     * Get class of setting model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function getSettingModel()
    {
        $model = config('setting.model');

        return new $model();
    }

    private function prepareSettingGroupInput()
    {
        if (property_exists($this, 'settingGroup')) {
            return str_slug($this->settingGroup, '.');
        }

        throw new \Exception('Invalid property or object \'settingGroup\'.');
    }

    private function prepareSettingNameInput(Request $request)
    {
        return property_exists($this,
            'settingName') ? $this->settingName : str_slug($this->prepareDisplayNameInput($request), '.');
    }

    private function prepareDisplayNameInput(Request $request)
    {
        return $request->input('display_name', '');
    }

    private function prepareSettingValueInput(Request $request)
    {
        $input = property_exists($this, 'settingValue') ? $this->settingValue : $request->input('setting_value');

        return is_array($input) ? json_encode($input) : json_encode([$input]);
    }

    private function getCacheName()
    {
        return sprintf('%s-%s', config('app.key'), 'setting');
    }

    private function getSettingCache()
    {
        return Cache::get($this->getCacheName(), null);
    }

    protected function updateSettingCache()
    {
        Cache::forget($this->getCacheName());

        return;
    }

    /**
     * @param array|string $data
     */
    private function warmupSettingCache($data)
    {
        Cache::put($this->getCacheName(), $data);

        return;
    }

}