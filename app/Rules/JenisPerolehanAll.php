<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Libraries\CheckerHelpers;

class JenisPerolehanAll implements Rule
{

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $checkerHelpers = new CheckerHelpers;
        if ($value != 'all') {
            $checkJenisPerolehan = $checkerHelpers->jenisPerolehanChecker(['uuid_jenis_perolehan' => $value]);
            if (is_null($checkJenisPerolehan)) :
                return false;
            endif;
        }

        return true;
    }

    public function message()
    {
        return 'jenis perolehan tidak ditemukan.';
    }
}
