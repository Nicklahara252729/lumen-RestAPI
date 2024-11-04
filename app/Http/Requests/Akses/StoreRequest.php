<?php

namespace App\Http\Requests\Akses;

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
            'role.*'        => [
                'required',
                Rule::in(['superadmin', 'admin', 'kabid', 'kasubbid', 'operator'])
            ],
            'uuid_bidang.*' => 'required|exists:App\Models\Bidang\Bidang\Bidang,uuid_bidang',
            'uuid_menu.*'   => 'required|exists:App\Models\Setting\Menu\Menu,uuid_menu',
            'uuid_akses.*'  => 'nullable'
        ];
    }
}
