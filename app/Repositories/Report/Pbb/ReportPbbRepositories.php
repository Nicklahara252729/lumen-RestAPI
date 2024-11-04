<?php

namespace App\Repositories\Report\Pbb;

interface ReportPbbRepositories
{
    public function rekapKetetapan(int $tahun);
    public function detailKetetapan(int $tahun);
    public function rincianKetetapan(object $request);
    public function rincianRealisasi(object $request);
    public function rekapRealisasi(string $startDate, string $endDate);
    public function detailRealisasi(string $startDate, string $endDate);
    public function rincianPiutang(object $request);
    public function rekapPiutang(object $request);
}
