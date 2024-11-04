<?php

namespace App\Repositories\Reklame;

interface ReklameRepositories
{
    public function data(int $status);
    public function verifikasi(array $request, string $reklameId);
    public function stsTertinggi();
    public function dataSts();
}
