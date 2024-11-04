<?php

namespace App\Repositories\Layanan\JenisLayanan;

interface JenisLayananRepositories
{
    public function data(string $status);
    public function store(array $request);
    public function update(array $request, string $uuidJenisLayanan);
    public function get(string $uuidJenisLayanan);
    public function delete(string $uuidJenisLayanan);
}
