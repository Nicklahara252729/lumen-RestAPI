<?php

namespace App\Repositories\Akses;

interface AksesRepositories
{
    public function data();
    public function store(array $request);
    public function get(string $uuidAkses);
    public function getByRoleBidang(string $role, string $uuidBidang);
}
