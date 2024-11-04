<?php

namespace App\Http\Requests\TagihanKolektor;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nomor_tagihan'   => 'required',
            'kode_bayar'      => 'required',
            'nop.*'           => 'required|integer',
            'tahun_pajak.*'   => 'required|integer',
            'total_tagihan.*' => 'required',
            'denda.*'         => 'required',
            'pokok.*'         => 'required',
            'nama_wp.*'       => 'required'
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'uuid_user' => authAttribute()['id']
        ]);
    }
}
