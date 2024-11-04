<?php

namespace App\Repositories\Kaban;

interface KabanRepositories
{
    public function sts();
    public function stsTertinggi();
    public function detailSts(int $noSts);
}
