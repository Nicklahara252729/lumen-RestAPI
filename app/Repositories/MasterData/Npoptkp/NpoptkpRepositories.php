<?php

namespace App\Repositories\MasterData\Npoptkp;

interface NpoptkpRepositories
{
    public function data();
    public function store(array $request);
    public function update(array $request, string $uuidNpoptkp);
    public function get(string $uuidNpoptkp);
    public function delete(string $uuidNpoptkp);
}
