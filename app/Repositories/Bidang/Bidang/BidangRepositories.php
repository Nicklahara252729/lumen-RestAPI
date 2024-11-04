<?php

namespace App\Repositories\Bidang\Bidang;

interface BidangRepositories
{
    public function data();
    public function store(array $request);
    public function update(array $request, string $uuidBidang);
    public function get(string $uuidBidang);
    public function delete(string $uuidBidang);
}
