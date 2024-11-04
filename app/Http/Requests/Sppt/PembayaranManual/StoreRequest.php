<?php

namespace App\Http\Requests\Sppt\PembayaranManual;

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
            'nop' => ['required','integer','max_digits:25',new Nop('sppt')],
            'tahun' => 'required|integer',
            'tanggal_bayar' => 'required',
            'bukti_bayar' => 'required|mimes:jpeg,png,jpg,pdf',
            'jumlah_tagihan' => 'required',
            'metode_pembayaran' => 'required',
            'tagihan_dibayar' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'created_by' => authAttribute()['id']
        ]);
    }
}
