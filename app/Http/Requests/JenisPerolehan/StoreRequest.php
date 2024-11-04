<?php

namespace App\Http\Requests\JenisPerolehan;

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
            'jenis_perolehan' => [
                'required',
                Rule::unique('jenis_perolehan')->where(function ($query) {
                    return $query->where([
                        'pelayanan' => $this->pelayanan
                    ]);
                })
            ],
            'pelayanan'  => 'required',
            'kode'  => [
                'required',
                Rule::unique('jenis_perolehan')->where(function ($query) {
                    return $query->where([
                        'pelayanan' => $this->pelayanan
                    ]);
                })
            ],
        ];
    }
}
