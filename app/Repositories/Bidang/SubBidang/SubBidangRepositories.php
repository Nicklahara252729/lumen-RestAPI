<?php

namespace App\Repositories\Bidang\SubBidang;

interface SubBidangRepositories
{
    public function data();
    public function store(array $request);
    public function update(array $request, string $uuidSubBidang);
    public function get(string $uuidSubBidang);
    public function getByBidang(string $param);
    public function delete(string $uuidSubBidang);
}
