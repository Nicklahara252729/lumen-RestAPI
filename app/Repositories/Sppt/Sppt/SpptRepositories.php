<?php

namespace App\Repositories\Sppt\Sppt;

interface SpptRepositories
{
    public function data(int $pageSize);
    public function history(string $kdKecamatan, string $kdKelurahan, string $kdBlok, string $noUrut, string $kdJnsOp, $request);
    public function search(string $kdKecamatan, string $kdKelurahan, string $nama, string $kdBlok, string $noUrut, string $statusKolektif);
    public function searchByNopTahun(string $kdKecamatan, string $kdKelurahan, string $kdBlok, string $noUrut, string $statusKolektif, string $tahun);
    public function searchByKtp(object $request);
    public function dataBlok(int $kdKecamatan, int $kdKelurahan);
    public function dataNopByBlok(int $kdKecamatan, int $kdKelurahan, int $kdBlok);
    public function dataBlokSelesai(int $kdKecamatan, int $kdKelurahan, string $uuidUser);
    public function dataNopByBlokSelesai(int $kdBlok);
    public function nopSelesai(string $nop);
}
