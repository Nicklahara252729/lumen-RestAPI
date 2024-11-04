<?php

namespace App\Repositories\Notaris;

interface NotarisRepositories
{
    public function data();
    public function verifikasi(string $uuidUser);
    public function update(string $uuidUser, array $request);
    public function get(string $uuidUser);
    public function delete(string $uuidUser);
    public function search(object $request);
}
