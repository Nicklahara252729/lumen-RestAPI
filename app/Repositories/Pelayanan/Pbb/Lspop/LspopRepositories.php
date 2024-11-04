<?php

namespace App\Repositories\Pelayanan\Pbb\Lspop;

interface LspopRepositories
{
    public function autocomplete($nop, $tahun);
    public function store(array $request);
}
