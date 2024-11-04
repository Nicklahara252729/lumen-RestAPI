<?php

namespace App\Repositories\User;

interface UserRepositories
{
    public function data();
    public function store(array $request);
    public function update(string $uuidUser, array $request);
    public function get(string $param);
    public function delete(string $uuidUser);
    public function updatePassword(string $uuidUser, array $request);
    public function search(object $request);
}
