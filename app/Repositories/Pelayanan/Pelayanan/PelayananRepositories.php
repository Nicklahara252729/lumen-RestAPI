<?php

namespace App\Repositories\Pelayanan\Pelayanan;

interface PelayananRepositories
{
    public function generate();
    public function data(string $param, int $pageSize);
    public function dataRealisasiKecamatan(string $kdKecamatan, int $tahun);
    public function dataPiutangKecamatan(string $kdKecamatan, int $tahun);
    public function dataJumlahWpKecamatan(string $kdKecamatan, int $tahun);
    public function dataRealisasiKelurahan(string $kdKecamatan, string $kdKelurahan, int $tahun);
    public function dataPiutangKelurahan(string $kdKecamatan, string $kdKelurahan, int $tahun);
    public function dataJumlahWpKelurahan(string $kdKecamatan, string $kdKelurahan, int $tahun);
    public function search(string $param, int $pageSize, object $request);
    public function countSppt(string $kdKecamatan, string $kdKelurahan, string $kdBlok, string $noUrutAwal, string $noUrutAkhir, string $statusKolektif);
    public function countNop(string $kdKecamatan, string $kdKelurahan);
}
