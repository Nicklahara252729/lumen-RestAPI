<?php

namespace App\Repositories\Pelayanan\Pbb\Mutasi;

interface MutasiRepositories
{
    public function store(array $storeRequest);
    public function autocompleteObjek(int $nop, int $tahun);
    public function autocompleteSubjek(int $nop, int $tahun);
}
