<?php

namespace App\Http\Requests\Bidang\Bidang;

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
            'nama_bidang' => 'required|unique:App\Models\Bidang\Bidang\Bidang,nama_bidang',
            'keterangan'  => 'nullable'
        ];
    }
}
