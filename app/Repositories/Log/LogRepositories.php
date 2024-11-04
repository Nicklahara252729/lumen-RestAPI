<?php

namespace App\Repositories\Log;

interface LogRepositories
{
    public function saveLog(string $action, string $keterangan, $uuidUser, $nop);
    public function saveLogBphtb(string $action, string $keterangan, $uuidUser, $noRegistrasi);
    public function logBphtb(string $noRegistrasi);
}
