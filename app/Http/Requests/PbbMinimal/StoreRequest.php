<?php

namespace App\Http\Requests\PbbMinimal;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'THN_PBB_MINIMAL'    => [
                'required',
                Rule::unique('pbb_minimal')->where(function ($query) {
                    return $query->where([
                        'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                        'KD_DATI2' => globalAttribute()['kdKota'],
                    ]);
                })
            ],
            'NO_SK_PBB_MINIMAL' => 'nullable',
            'TGL_SK_PBB_MINIMAL'=> 'nullable',
            'NILAI_PBB_MINIMAL' => 'nullable',
            'TGL_JATUH_TEMPO'   => [
                'required',
                Rule::unique('pbb_minimal')->where(function ($query) {
                    return $query->where([
                        'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                        'KD_DATI2' => globalAttribute()['kdKota'],
                        'THN_PBB_MINIMAL' => $this->THN_PBB_MINIMAL,
                    ]);
                })
            ],
            'NIP_PEREKAM_PBB_MINIMAL' => 'nullable'
        ];
    }
}
