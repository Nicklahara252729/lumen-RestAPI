<?php

namespace App\Http\Requests\OperatorLapangan;

use App\Http\Requests\FormRequest;
use App\Rules\Nopd;
use Illuminate\Validation\Rule;
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
            'nopd'           => ['required', new Nopd()],
            'nama_objek'     => 'required',
            'nama_pemilik'   => 'required',
            'alamat_objek'   => 'required',
            'alamat_pemilik' => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'photo'          => 'required|mimes:jpeg,png,jpg'
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'updated_by' => authAttribute()['id'],
            'updated_at' => Carbon::now()->toDateTimeLocalString()
        ]);
    }
}
