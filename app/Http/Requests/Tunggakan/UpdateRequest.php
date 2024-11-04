<?php

namespace App\Http\Requests\Tunggakan;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'latitude' => 'required',
            'longitude' => 'required',
            // 'photo' => 'required|mimes:jpeg,png,jpg|max:2048',
            // 'kategori' => 'required|in:k1,k2,k3,k4'
            'photo' => 'required',
            'kategori' => 'nullable',
            'keterangan_photo' => 'nullable'
        ];
    }
}
