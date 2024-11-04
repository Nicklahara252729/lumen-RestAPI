<?php

namespace App\Repositories\Setting\Menu;

interface MenuRepositories
{
    public function data();
    public function store(array $request);
    public function update(string $uuidMenu, array $request);
    public function get(string $uuidMenu);
    public function delete(string $uuidMenu);
}
