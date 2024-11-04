<?php

namespace App\Http\Requests\Layanan\JenisLayanan;

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
            'jenis_layanan' => 'required|unique:App\Models\Layanan\JenisLayanan\JenisLayanan,jenis_layanan',
        ];
    }
}
