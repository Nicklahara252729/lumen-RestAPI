<?php

namespace App\Repositories\JenisPerolehan;

interface JenisPerolehanRepositories
{
    public function data(string $pelayanan);
    public function store(array $request);
    public function update(array $request, string $uuidJenisPerolehan);
    public function get(string $uuidJenisPerolehan);
    public function delete(string $uuidJenisPerolehan);
}
