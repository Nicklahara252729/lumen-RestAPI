<?php

namespace App\Repositories\Layanan\Layanan;

interface LayananRepositories
{
    public function data();
    public function store(array $request);
    public function update(array $request, string $uuidLayanan);
    public function get(string $uuidLayanan);
    public function delete(string $uuidLayanan);
}
