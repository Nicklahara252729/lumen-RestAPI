<?php

namespace App\Http\Requests\Bidang\SubBidang;

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
            'nama_sub_bidang' => ['required', Rule::unique("sub_bidang", "nama_sub_bidang")->ignore($this->id, "uuid_sub_bidang")],
            'uuid_bidang'  => 'required|exists:App\Models\Bidang\Bidang\Bidang,uuid_bidang',
        ];
    }
}
