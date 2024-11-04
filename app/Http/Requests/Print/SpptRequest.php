<?php

namespace App\Http\Requests\Print;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class SpptRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'KD_KECAMATAN'    => 'required|exists:App\Models\Refrensi\Kecamatan\Kecamatan,KD_KECAMATAN',
            'KD_KELURAHAN'    => [
                'required',
                Rule::exists('ref_kelurahan')->where(function ($query) {
                    return $query->where([
                        'KD_KECAMATAN' => $this->KD_KECAMATAN,
                    ]);
                })
            ],
            'KD_BLOK'    => [
                'required',
                Rule::exists('dat_peta_blok')->where(function ($query) {
                    return $query->where([
                        'KD_KECAMATAN' => $this->KD_KECAMATAN,
                        'KD_KELURAHAN' => $this->KD_KELURAHAN,
                    ]);
                })
            ],
            'NO_URUT'    => [
                'required',
                Rule::exists('sppt')->where(function ($query) {
                    return $query->where([
                        'KD_KECAMATAN' => $this->KD_KECAMATAN,
                        'KD_KELURAHAN' => $this->KD_KELURAHAN,
                        'NO_URUT' => $this->NO_URUT,
                    ]);
                })
            ],
            'status_kolektif' => ['required', Rule::in([0, 1, 7])],
            'tahun' => 'required',
        ];
    }
}
