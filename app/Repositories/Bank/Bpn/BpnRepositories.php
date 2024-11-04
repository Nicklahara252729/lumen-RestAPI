<?php

namespace App\Repositories\Bank\Bpn;

interface BpnRepositories
{
    public function bphtbService(string $uuidPelayananBphtb);
    public function getBPHTBService(object $requset);
    public function getPBBService();
}
