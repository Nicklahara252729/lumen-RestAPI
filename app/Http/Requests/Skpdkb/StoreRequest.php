<?php

namespace App\Http\Requests\Skpdkb;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sspd' => 'required|exists:pelayanan_bphtb,no_registrasi',
            'tanggal_skpdkb' => 'required|date',
            'npop' => 'required',
            'npoptkp' => 'required',
            'nilai_pajak' => 'required',
            'total_bphtb' => 'required'
        ];
    }
}
