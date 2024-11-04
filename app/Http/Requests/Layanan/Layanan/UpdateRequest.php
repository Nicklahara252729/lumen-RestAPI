<?php

namespace App\Http\Requests\Layanan\Layanan;

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
            'layanan' =>  ['required', Rule::unique("layanan", "layanan")->ignore($this->id, "uuid_layanan")],
            'icon' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
