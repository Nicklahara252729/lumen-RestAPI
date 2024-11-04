<?php

namespace App\Http\Requests\Akses;

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
            'role'        => [
                'required',
                Rule::in(['superadmin', 'admin', 'kabid', 'kasubbid', 'operator']),
                Rule::unique('akses')->where(function ($query) {
                    $query->where([
                        'uuid_bidang' => $this->uuid_bidang,
                        'uuid_menu' => $this->uuid_menu,
                    ]);
                    return $query->whereNot(function (Builder $query) {
                        $query->where('uuid_akses', $this->id);
                    });
                })
            ],
            'uuid_bidang' => 'required|exists:App\Models\Bidang\Bidang\Bidang,uuid_bidang',
            'uuid_menu'   => 'required|exists:App\Models\Setting\Menu\Menu,uuid_menu',
        ];
    }
}
