<?php

namespace App\Repositories\Pelayanan\Pbb\GabungNop;

interface GabungNopRepositories
{
    public function store(array $request);
    public function autocomplete(string $nop, int $tahun);
}
