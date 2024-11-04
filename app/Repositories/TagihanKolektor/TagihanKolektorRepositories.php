<?php

namespace App\Repositories\TagihanKolektor;

interface TagihanKolektorRepositories
{
    public function data(string $pageSize);
    public function get(int $nop);
    public function store(array $request);
}
