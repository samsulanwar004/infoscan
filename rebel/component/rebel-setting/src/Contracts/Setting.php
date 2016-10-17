<?php

namespace Rebel\Component\Setting\Contracts;

interface Setting
{
    /**
     * Get new instance of the setting model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel();

    /**
     * Get first row with column refference.
     *
     * @param string|mixed $value
     * @param string $column
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($value, $column = 'group_name');

    /**
     * Select all data of setting.
     *
     * @param array $ignoredGroups
     * @param bool $isVisible
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($ignoredGroups = [], $isVisible = true);

    /**
     * Get or find setting data by id of data in database.
     *
     * @param int $value
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById($value);

    /**
     * Get or find setting by setting name.
     *
     * @param $value
     *
     * @return mixed
     */
    public function getByName($value);

    /**
     * Get or find setting by group name.
     *
     * @param $value
     *
     * @return mixed
     */
    public function getByGroup($value);

    /**
     * Create a new setting data.
     *
     * @param string $group
     * @param string $name
     * @param string $value
     * @param string $createdBy
     * @param string string $displayName
     * @param bool $isVisible
     *
     * @return bool|mixed
     */
    public function createNew($group, $name, $value, $createdBy, $displayName = '', $isVisible = true);

    /**
     * Update setting data by 'id' refferece.
     *
     * @param $id
     * @param array $data
     *
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Update general checksum of setting data.
     *
     * @return mixed
     */
    public function updateChecksum();
}
