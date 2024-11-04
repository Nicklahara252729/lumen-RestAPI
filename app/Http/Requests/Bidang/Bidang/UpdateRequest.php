<?php

namespace App\Http\Requests\Bidang\Bidang;

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
            'nama_bidang' => ['required', Rule::unique("bidang", "nama_bidang")->ignore($this->id, "uuid_bidang")],
            'keterangan'  => 'nullable'
        ];
    }
}
