<?php

namespace App\Repositories\Setting\Layanan;

interface LayananRepositories
{
    public function updateStatus(array $request, string $uuidSetting);
}
