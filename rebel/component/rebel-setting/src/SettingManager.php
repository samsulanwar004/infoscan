<?php

namespace Rebel\Component\Setting;

use Rebel\Component\Setting\Contracts\Setting;

class SettingManager implements Setting
{

    const CHECKSUM_SETTING_GROUP = 'app';
    const CHECKSUM_SETTING_NAME  = 'app.checksum';
    /**
     * @var string $modelName
     */
    private $modelName;
    /**
     * @var array $config
     */
    private $config;
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     */
    private $model;
    /**
     * @var string $user
     */
    private $user = '';


    public function __construct(array $config)
    {
        $this->config = $config;

        $model = $this->modelName = $this->config['model'];
        $this->model = new $model;
    }

    /**
     * @inheritdoc
     */
    public function get($value, $column = 'group')
    {
        return $this->model->where($column, '=', $value)->get();
    }

    /**
     * @inheritdoc
     */
    public function all($ignoredGroups = [], $isVisible = true)
    {
        $settings = $this->model
            ->where('is_visible', $isVisible);


        foreach ($ignoredGroups as $ig) {
            $parameter = $ig;
            if (preg_match('/\.\*/', $ig)) {
                //remove the '*' character and add query where not like !
                $parameter = str_replace(['.*'], '', $ig);
            }

            $settings->where('setting_group', 'NOT LIKE', '%' . $parameter . '%');
        }

        return $settings->get();
    }

    /**
     * @inheritdoc
     */
    public function getById($value)
    {
        return $this->model->where('id', '=', $value)->first();
    }

    /**
     * @inheritdoc
     */
    public function getByName($value, $single = true)
    {
        $data = $this->model->where('setting_name', '=', $value);

        return !$single ? $data->get() : $data->first();
    }

    /**
     * @inheritdoc
     */
    public function getByGroup($value)
    {
        return $this->model
            ->orderBy('id', 'DESC')
            ->where('setting_group', '=', $value)
            ->get();
    }

    /**
     * @inheritdoc
     */
    public function createNew($group, $name, $value, $createdBy, $displayName = '', $isVisible = true)
    {
        $set = $this->model;
        $set->setting_group = $group;
        $set->setting_name = $name;
        $set->setting_display_name = $displayName;
        $set->is_visible = $isVisible;
        $set->created_by = $createdBy;
        $set->setting_value = is_array($value) ? json_encode($value) : json_encode([$value]);

        if (!$set->save()) {
            throw new \Exception('Can\'t create a new record !');
        }

        $this->updateChecksum();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function update($id, array $data)
    {
        $set = $this->getById($id);
        if (!$set) {
            throw new \Exception('There is no setting with id: ' . $id);
        }

        $set->setting_group = $data['group'];
        $set->setting_name = $data['name'];
        $set->setting_display_name = isset($data['display_name']) ? $data['display_name'] : '';
        $set->is_visible = isset($data['is_visible']) ? (bool)$data['is_visible'] : 0;
        $set->created_by = $this->user;

        $value = isset($data['value']) ? $data['value'] : [];
        $set->setting_value = is_array($value) ? json_encode($value) : json_encode([$value]);

        if (!$set->save()) {
            throw new \Exception('Can\'t update this data !');
        }

        $this->updateChecksum();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function updateChecksum()
    {
        $token = md5(str_random());
        $s = $this->getByName(self::CHECKSUM_SETTING_NAME);

        if ($s) {
            $s->setting_value = json_encode([$token]);
            $s->updated_by = $this->user;

            return $s->save();
        }

        $m = $this->modelName;
        $ns = new $m;
        $ns->setting_group = self::CHECKSUM_SETTING_GROUP;
        $ns->setting_name = self::CHECKSUM_SETTING_NAME;
        $ns->setting_value = json_encode([$token]);
        $ns->created_by = $this->user;
        $ns->is_visible = true;

        return $ns->save();
    }

    /**
     * @inheritdoc
     */
    public function isValidChecksum($current)
    {
        if ($token = $this->getByName(self::CHECKSUM_SETTING_NAME)) {
            $value = json_decode($token->setting_value, true);

            return $value[0] === $current;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
