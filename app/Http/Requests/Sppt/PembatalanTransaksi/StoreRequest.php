<?php

namespace App\Http\Requests\Sppt\PembatalanTransaksi;

use App\Http\Requests\FormRequest;
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
            'nop' => ['required', 'integer', 'max_digits:25', new Nop('sppt', $this->tahun)],
            'tahun' => 'required|integer',
            'alasan' => 'required',
            'bukti' => 'required|mimes:jpeg,png,jpg,pdf',
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'created_by' => authAttribute()['id']
        ]);
    }
}
