<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class NoSTS implements Rule
{

    private $db;

    public function __construct()
    {
        $this->db = DB::connection('second_mysql');;
    }

    public function passes($attribute, $value)
    {
        $checkNoSts = $this->db->table('STS_History')->where('No_STS', $value)->first();
        if (!is_null($checkNoSts)) :
            return false;
        endif;

        return true;
    }

    public function message()
    {
        return 'No STS sudah ada.';
    }
}
