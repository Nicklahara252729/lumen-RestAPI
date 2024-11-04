<?php

namespace App\Repositories\Region\Kelurahan;

interface KelurahanRepositories
{
    public function data(int $idKecamatan);
    public function get(string $param);
}
