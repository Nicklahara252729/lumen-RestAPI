<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Libraries\CheckerHelpers;

class UserAll implements Rule
{

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $checkerHelpers = new CheckerHelpers;
        if ($value != 'all') {
            $checkUser = $checkerHelpers->userChecker(['uuid_user' => $value]);
            if (is_null($checkUser)) :
                return false;
            endif;
        }

        return true;
    }

    public function message()
    {
        return 'user tidak ditemukan.';
    }
}
