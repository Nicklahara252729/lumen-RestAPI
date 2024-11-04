<?php

namespace App\Http\Requests\Pelayanan\Bphtb;

use App\Http\Requests\FormRequest;

class StoreStatusDitolak extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'no_registrasi'        => 'required|exists:pelayanan_bphtb,no_registrasi',
        ];
    }
}
