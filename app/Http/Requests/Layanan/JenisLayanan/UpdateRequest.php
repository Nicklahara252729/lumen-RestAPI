<?php

namespace App\Http\Requests\Layanan\JenisLayanan;

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
            'jenis_layanan' =>  ['required', Rule::unique("jenis_layanan", "jenis_layanan")->ignore($this->id, "uuid_jenis_layanan")],
        ];
    }
}
