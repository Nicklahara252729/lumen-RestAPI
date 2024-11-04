<?php

namespace App\Http\Requests\Notaris;

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
            'name'               => 'required|string',
            'password'           => 'required',
            'alamat'             => 'required',
            'kota'               => 'required',
            'no_hp'              => 'required|unique:App\Models\User\User,no_hp',
            'kontak_person'      => 'required|unique:App\Models\User\User,kontak_person',
        ];
    }
}
