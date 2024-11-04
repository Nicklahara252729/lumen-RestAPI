<?php

namespace App\Http\Requests\OperatorLapangan;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Traits\Generator;

class StoreNopdRequest extends FormRequest
{
    use Generator;
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'npwpd'       => 'required',
            'jenis_pajak' => 'required',
            'nama_usaha'  => 'required',
            'alamat'      => 'required',
            'kecamatan_id'   => 'required',
            'kelurahan_id'   => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'photo'       => 'required|mimes:jpeg,png,jpg'
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'updated_by' => authAttribute()['id'],
            'updated_at' => Carbon::now()->toDateTimeLocalString(),
            'nopd'       => $this->nopd($this->req->jenis_pajak)
        ]);
    }
}
