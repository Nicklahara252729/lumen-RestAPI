<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Libraries\CheckerHelpers;

class ConfirmPassword implements Rule
{
    protected $uuidUser;
    protected $currentPassword;

    public function __construct($uuidUser, $currentPassword)
    {
        $this->uuidUser = $uuidUser;
        $this->currentPassword = $currentPassword;
    }

    public function passes($attribute, $value)
    {

        $checkHelper = new CheckerHelpers;
        $getOldPassword = $checkHelper->userChecker(['uuid_user' => $this->uuidUser]);
        if (!Hash::check($this->currentPassword, $getOldPassword->password)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'Your current password is incorrect.';
    }
}
