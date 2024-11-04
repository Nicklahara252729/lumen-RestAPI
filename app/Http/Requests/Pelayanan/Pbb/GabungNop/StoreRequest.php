<?php

namespace App\Http\Requests\Pelayanan\Pbb\GabungNop;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Nop;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nomor_pelayanan'       => 'required|unique:App\Models\Pelayanan\Pelayanan\Pelayanan,nomor_pelayanan',
            'uuid_layanan'          => 'required|exists:layanan,uuid_layanan',
            'uuid_jenis_pelayanan'  => 'required|exists:jenis_layanan,uuid_jenis_layanan',
            'nop_awal'              => ['required', 'integer', 'max_digits:25', new Nop('sppt')],
            'nop_gabung'            => ['required', 'integer', 'max_digits:25', new Nop('sppt')],
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'status_verifikasi' => (int)1
        ]);
    }
}
