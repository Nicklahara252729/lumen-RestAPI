<?php

namespace App\Repositories\Dashboard;

interface DashboardRepositories
{
    public function totalPermohonan();
    public function totalPerLayanan();
    public function dataByKecamatanOrKelurahan(string $kdKecamatan, string $kdKelurahan);
    public function totalPermohonanBphtb();
}
