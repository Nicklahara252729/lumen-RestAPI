<?php

namespace App\Http\Requests\Print;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\KdBlokRule;
use App\Rules\NoUrut;

class SpptMasalMultipleRequest extends FormRequest
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
                'array',
                new KdBlokRule('dat_peta_blok', 'KD_BLOK', $this->KD_KECAMATAN, $this->KD_KELURAHAN)
            ],
            'no_urut_awal'    => [
                'required',
                'array',
                new NoUrut('sppt', 'NO_URUT', $this->KD_KECAMATAN, $this->KD_KELURAHAN, 'awal')
            ],
            'no_urut_akhir'    => [
                'required',
                'array',
                new NoUrut('sppt', 'NO_URUT', $this->KD_KECAMATAN, $this->KD_KELURAHAN, 'akhir')
            ],
            'status_kolektif.*' => ['required', Rule::in([0, 1, 7])],
        ];
    }
}
