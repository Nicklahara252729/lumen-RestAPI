<?php

namespace App\Repositories\Setting\General;

interface SettingRepositories
{
    public function data();
    public function get(string $param);
    public function update(array $request, string $uuidSetting);
}
