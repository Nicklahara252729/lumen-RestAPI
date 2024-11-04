<?php

namespace App\Http\Requests\MasterData\Npoptkp;

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
            'nilai' => 'required|integer',
            'tahun'  => 'required|integer',
            'uuid_jenis_perolehan' => [
                'required',
                Rule::unique('npoptkp')->where(function ($query) {
                    return $query->where([
                        'tahun' => $this->tahun
                    ]);
                })
            ],
            'nilai_pajak' => 'required'
        ];
    }
}
