<?php

namespace App\Repositories\Token;

interface TokenRepositories
{
    public function validation();
    public function refresh();
}
