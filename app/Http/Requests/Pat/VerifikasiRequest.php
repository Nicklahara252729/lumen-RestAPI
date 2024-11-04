<?php

namespace App\Http\Requests\Pat;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Carbon;

class VerifikasiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'required'
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'user_verifi' => authAttribute()['id'],
            'date_verifikasi' => Carbon::now()->toDateTimeLocalString()
        ]);
    }
}