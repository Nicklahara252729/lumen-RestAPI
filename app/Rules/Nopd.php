<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class Nopd implements Rule
{

    private $db;

    public function __construct()
    {
        $this->db = DB::connection('third_mysql');;
    }

    public function passes($attribute, $value)
    {
        $checkNopd = $this->db->table('x_nopd')->where('nopd', $value)->first();
        if (is_null($checkNopd)) :
            return false;
        endif;

        return true;
    }

    public function message()
    {
        return 'NOPD tidak ditemukan.';
    }
}
