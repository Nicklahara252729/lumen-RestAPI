<?php

namespace App\Repositories\Sppt\PembayaranManual;

interface PembayaranManualRepositories
{
    public function store(array $request);
    public function autocomplete(string $nop, int $tahun);
    public function data();
}
