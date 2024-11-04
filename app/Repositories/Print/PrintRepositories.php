<?php

namespace App\Repositories\Print;

interface PrintRepositories
{
    public function permohonan(string $pelayanan);
    public function suratKeteranganNjop(string $pelayanan);
    public function sppt(array $spptRequest);
    public function spptMasal(string $kdKecamatan, string $kdKelurahan, string $kdBlok, int $tahun, string $key);
    public function stts(string $kdKecamatan, string $kdKelurahan, string $kdBlok, string $noUrut, string $statusKolektif, int $tahun);
    public function spptMasalMultiple(array $request);
    public function sspd(string $uuidPelayananBphtb);
    public function skpdkb(string $uuidSkpdkb);
}
