<?php

namespace App\Repositories\Pengutip;

interface PengutipRepositories
{
    public function data();
    public function dataRestoran();
    public function autocomplete(int $nopd);
    public function store(array $request);
}
