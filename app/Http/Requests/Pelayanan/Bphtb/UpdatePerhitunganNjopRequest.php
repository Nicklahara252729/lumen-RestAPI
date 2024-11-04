<?php

namespace App\Http\Requests\Pelayanan\Bphtb;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerhitunganNjopRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'luas_tanah'           => 'required|integer',
            'luas_bangunan'        => 'required|integer',
        ];
    }
}
