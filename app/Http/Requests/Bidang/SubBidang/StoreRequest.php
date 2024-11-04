<?php

namespace App\Http\Requests\Bidang\SubBidang;

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
            'nama_sub_bidang' => 'required|unique:App\Models\Bidang\SubBidang\SubBidang,nama_sub_bidang',
            'uuid_bidang'  => 'required|exists:App\Models\Bidang\Bidang\Bidang,uuid_bidang',
        ];
    }
}
