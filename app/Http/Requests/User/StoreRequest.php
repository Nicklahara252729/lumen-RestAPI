<?php

namespace App\Http\Requests\User;

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
            'email'              => 'required|unique:App\Models\User\User,email',
            'username'           => 'required|unique:App\Models\User\User,username',
            'password'           => 'required',
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
                    'admin_kolektor',
                    'pengutip',
                    'operator_lapangan'
                ]),
            ],
            'profile_photo_path' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'uuid_bidang'        => 'nullable',
            'uuid_sub_bidang'    => 'nullable',
            'nip'                => 'nullable|unique:App\Models\User\User,nip',
            'no_hp'              => 'required|unique:App\Models\User\User,no_hp',
            'kd_kecamatan'       => 'nullable|exists:ref_kecamatan,KD_KECAMATAN',
            'kd_kelurahan'       => 'nullable|exists:ref_kelurahan,KD_KELURAHAN',
        ];
    }
}
