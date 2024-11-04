<?php

namespace App\Http\Requests\JenisPerolehan;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jenis_perolehan'   => [
                'required',
                Rule::unique('jenis_perolehan')->where(function ($query) {
                    $query->where([
                        'pelayanan' => $this->pelayanan
                    ]);
                    return $query->whereNot(function (Builder $query) {
                        $query->where([
                            'uuid_jenis_perolehan' => $this->id
                        ]);
                    });
                })
            ],
            'pelayanan'  => 'required',
            'kode'  => [
                'required',
                Rule::unique('jenis_perolehan')->where(function ($query) {
                    return $query->where([
                        'pelayanan' => $this->pelayanan
                    ]);
                })->ignore($this->id, 'uuid_jenis_perolehan')
            ]
        ];
    }
}
