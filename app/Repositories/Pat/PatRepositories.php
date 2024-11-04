<?php

namespace App\Repositories\Pat;

interface PatRepositories
{
    public function data(int $status);
    public function verifikasi(array $request, string $idPat);
    public function stsTertinggi();
    public function dataSts();
}
