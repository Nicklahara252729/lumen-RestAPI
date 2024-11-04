<?php

namespace App\Http\Requests\User;

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
            'name'               => ['required', Rule::unique("users", "name")->ignore($this->id, "uuid_user")],
            'email'              => ['required', Rule::unique("users", "email")->ignore($this->id, "uuid_user")],
            'username'           => ['required', Rule::unique("users", "username")->ignore($this->id, "uuid_user")],
            'role'               => [
                'required',
                'string',
                Rule::in([
                    'superadmin',
                    'admin',
                    'kabid',
                    'kasubbid',
                    'operator',
                    'kecamatan',
                    'kelurahan',
                    'notaris',
                    'umum',
                    'petugas lapangan',
                    'kaban',
                    'kolektor',
                    'admin_kolektor'
                ]),
            ],
            'profile_photo_path' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'uuid_bidang'        => 'nullable',
            'uuid_sub_bidang'    => 'nullable',
            'nip'                => ['nullable', Rule::unique("users", "nip")->ignore($this->id, "uuid_user")],
            'no_hp'              => ['required', Rule::unique("users", "no_hp")->ignore($this->id, "uuid_user")],
            'kd_kecamatan'       => 'nullable',
            'kd_kelurahan'       => 'nullable',
        ];
    }
}