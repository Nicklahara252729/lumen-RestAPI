<?php

namespace App\Repositories\Pelayanan\Pbb\PecahNop;

interface PecahNopRepositories
{
    public function store(array $request);
    public function updateStatusVerifikasi(array $request, string $uuidPelayanan);
    public function autocomplete(string $nop);
    public function data(int $pageSize);
}
