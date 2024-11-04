<?php

namespace App\Http\Requests\Auth\Register;

use App\Http\Requests\FormRequest;

class StoreNotarisRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'               => 'required|string',
            'password'           => 'required',
            'alamat'             => 'required',
            'kota'               => 'required',
            'no_hp'              => 'required|unique:App\Models\User\User,no_hp',
            'kontak_person'      => 'required|unique:App\Models\User\User,kontak_person',
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'role' => 'notaris'
        ]);
    }
}
