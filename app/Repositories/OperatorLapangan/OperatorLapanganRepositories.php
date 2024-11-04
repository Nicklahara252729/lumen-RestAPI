<?php

namespace App\Repositories\OperatorLapangan;

interface OperatorLapanganRepositories
{
    public function data();
    public function dataHiburan();
    public function dataHotel();
    public function dataParkir();
    public function dataPat();
    public function dataPenerangan();
    public function dataPln();
    public function dataReklame();
    public function dataWalet();
    public function autocomplete(string $key);
    public function store(array $request);
    public function storeRegpribadi(array $request);
    public function storeNopd(array $request);
    public function dataRegpribadi();
    public function dataNopd();
    public function search(string $key, string $idKecamatan, string $idKelurahan);
}
