<?php

namespace App\Http\Requests\Layanan\Layanan;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'layanan' => 'required|unique:App\Models\Layanan\Layanan\Layanan,layanan',
            'icon' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
