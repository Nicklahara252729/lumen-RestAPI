<?php

namespace App\Http\Requests\Notaris;

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
            'name'               => 'required|string',
            'alamat'             => 'required',
            'kota'               => 'required',
            'no_hp'              => ['required', Rule::unique('users', 'no_hp')->ignore($this->id, 'uuid_user')],
            'kontak_person'      => ['required', Rule::unique('users', 'kontak_person')->ignore($this->id, 'uuid_user')],
        ];
    }
}
