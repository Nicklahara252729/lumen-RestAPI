<?php

namespace App\Http\Requests\Report\Notaris;

use App\Http\Requests\FormRequest;
use App\Rules\UserAll;
use App\Rules\JenisPerolehanAll;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'uuid_user' => ['required', new UserAll()],
            'start_date'  => 'required',
            'end_date' => 'required',
            'status_bayar' => 'required|in:all,0,1',
            'uuid_jenis_perolehan' => ['required', new JenisPerolehanAll()]
        ];
    }
}
