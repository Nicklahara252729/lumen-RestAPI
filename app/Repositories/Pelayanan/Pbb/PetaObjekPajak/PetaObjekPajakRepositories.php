<?php

namespace App\Repositories\Pelayanan\Pbb\PetaObjekPajak;

interface PetaObjekPajakRepositories
{
    public function store(array $request);
    public function data();
    public function autocomplete(int $nop);
}
