<?php

namespace App\Http\Requests\OperatorLapangan;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\Generator;
use Illuminate\Support\Carbon;

class StoreRegpribadiRequest extends FormRequest
{
    use Generator;
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama_usaha'    => 'required',
            'nama_pemilik'  => 'required',
            'alamat'        => 'required',
            'kecamatan_id'     => 'nullable',
            'kelurahan_id'     => 'nullable',
            'no_telp'       => 'required',
            'jenis_pajak'   => 'required|in:1,2'
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'created_by' => authAttribute()['id'],
            'date_created' => Carbon::now()->toDateTimeLocalString(),
            'npwpd' => $this->npwpd($this->req->jenis_pajak, $this->req->kecamatan_id, $this->req->kelurahan_id),
        ]);
    }
}
