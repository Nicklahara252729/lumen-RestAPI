<?php

namespace App\Repositories\Region\Kecamatan;

interface KecamatanRepositories
{
    public function data(int $idKabupaten);
    public function get(string $param);
}
