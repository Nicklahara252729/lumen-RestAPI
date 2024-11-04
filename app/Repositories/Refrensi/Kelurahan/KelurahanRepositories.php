<?php

namespace App\Repositories\Refrensi\Kelurahan;

interface KelurahanRepositories
{
    public function getAll();
    public function data(string $kdKecamatan);
    public function dataPAD(string $kecamatanId);
}
