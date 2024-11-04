<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Libraries\CheckerHelpers;

class Nop implements Rule
{

    private $table;
    private $tahun;

    public function __construct($table, $tahun = null)
    {
        $this->table = $table;
        $this->tahun = $tahun;
    }

    public function passes($attribute, $value)
    {
        $checkNop = DB::table($this->table)->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = ?', [$value]);
        if (!is_null($this->tahun)) :
            $checkNop = $checkNop->where('THN_PAJAK_SPPT',$this->tahun);
        endif;
        $checkNop = $checkNop->first();
        if (is_null($checkNop)) :
            return false;
        endif;

        return true;
    }

    public function message()
    {
        return 'NOP tidak ditemukan.';
    }
}
