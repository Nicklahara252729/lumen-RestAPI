<?php

namespace App\Repositories\Pelayanan\Pbb\Pendaftaran;

interface PendaftaranRepositories
{
    public function store(array $request);
    public function storeLspop(array $request);
    public function get(string $param);
    public function updateStatusVerifikasi(array $request, string $uuidPelayanan);
    public function delete(string $uuidPelayanan, string $uuidUser);
    public function update(string $uuidPelayanan, array $request);
}
