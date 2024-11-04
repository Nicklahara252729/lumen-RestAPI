<?php

namespace App\Repositories\Pelayanan\Bphtb;

interface BphtbRepositories
{
    public function store(array $request);
    public function data(int $statusVerifikasi, int $pageSize, string $deleted);
    public function autocomplete(string $nop, int $tahun);
    public function get(string $param);
    public function update(array $request, string $uuidPelayananBphtb);
    public function updateStatusVerifikasi(array $request, string $uuidPelayananBphtb);
    public function riwayatDitolak(string $noRegistrasi);
    public function detail(string $noRegistrasi);
    public function storeStatusDitolak(array $request);
    public function search(array $condition, int $pageSize, string $deleted);
    public function updatePerhitunganNjop(array $request, string $uuidPelayananBphtb);
    public function updatePerhitunganBphtb(array $request, string $uuidPelayananBphtb);
    public function deleteDokumen(string $uuidPelayananBphtb, string $dokumen);
    public function delete(string $uuidPelayananBphtb);
    public function storePembayaranManual(array $request);
    public function updateFull(array $request, string $uuidPelayananBphtb);
}
