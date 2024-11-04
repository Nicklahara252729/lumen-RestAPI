<?php

namespace App\Http\Requests\Pelayanan\Bphtb;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusVerifikasiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status_verifikasi' => 'required',
            'no_registrasi' => 'nullable|exists:pelayanan_bphtb,no_registrasi',
            'uuid_user' => 'nullable|exists:users,uuid_user',
            'keterangan' => 'nullable',
            'harga' => 'nullable|integer',
            'pengurangan' => 'nullable|integer'
        ];
    }
}
