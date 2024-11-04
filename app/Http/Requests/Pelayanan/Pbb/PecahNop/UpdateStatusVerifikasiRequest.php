<?php

namespace App\Http\Requests\Pelayanan\Pbb\PecahNop;

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
            'status_verifikasi' => ['required', Rule::in([1, 2, 3, 4])],
            'alasan'            => 'nullable'
        ];
    }
}
