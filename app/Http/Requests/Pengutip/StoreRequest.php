<?php

namespace App\Http\Requests\Pengutip;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Carbon;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama_op' => 'required',
            'alamat' => 'required',
            'tanggal_bayar' => 'required',
            'masa_pajak' => 'required',
            'jumlah' => 'required'
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'created_by' => authAttribute()['id'],
            'created_at' => Carbon::now()->toDateTimeLocalString(),
            'updated_at' => Carbon::now()->toDateTimeLocalString()
        ]);
    }
}
