<?php

namespace App\Repositories\PetaObjekPajak;

interface PetaObjekPajakRepositories
{
    public function data(string $kdKecamatan, string $kdKelurahan, string $blok);
}
