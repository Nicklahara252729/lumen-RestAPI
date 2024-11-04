<?php

namespace App\Repositories\PbbMinimal;

interface PbbMinimalRepositories
{
    public function data();
    public function store(array $request);
    public function update(array $request, string $thnPbbMinimal);
    public function get(string $thnPbbMinimal);
    public function delete(string $thnPbbMinimal);
}
