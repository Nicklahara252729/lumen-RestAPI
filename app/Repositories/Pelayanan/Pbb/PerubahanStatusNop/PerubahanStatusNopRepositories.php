<?php

namespace App\Repositories\Pelayanan\Pbb\PerubahanStatusNop;

interface PerubahanStatusNopRepositories
{
    public function update(array $request);
    public function data(int $pageSize);
}
