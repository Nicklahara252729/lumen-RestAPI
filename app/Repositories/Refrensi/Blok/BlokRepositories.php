<?php

namespace App\Repositories\Refrensi\Blok;

interface BlokRepositories
{
    public function data(string $kdKecamatan, string $kdKelurahan);
    public function getAll();
}
