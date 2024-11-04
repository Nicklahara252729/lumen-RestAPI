<?php

namespace App\Http\Requests\Setting\Menu;

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
            'nama_menu'    => 'required',
            'link'         => 'required',
            'icon'         => 'required',
            'is_main_menu' => 'nullable|exists:App\Models\Setting\Menu\Menu,uuid_menu',
        ];
    }
}
