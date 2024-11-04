<?php

namespace App\Http\Requests\Pelayanan\Pbb\PerubahanStatusNop;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'nop'                   => 'required',
            'jenis_objek_pajak'     => 'required',
            'nama_wp'               => 'required',
            'alamat'                => 'required',
        ];
    }
}
