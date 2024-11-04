<?php

namespace App\Repositories\Tunggakan;

interface TunggakanRepositories
{
    public function data(string $kdKecamatan, string $kdKelurahan);
    public function dataNopByKdBlok(int $kdBlok, string $kdKecamatan, string $kdKelurahan);
    public function update(array $request, string $param);
}
