<?php

namespace App\Http\Requests\Pelayanan\Bphtb;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerhitunganBphtbRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'npoptkp' => 'required'
        ];
    }
}
