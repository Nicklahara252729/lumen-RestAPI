<?php

namespace App\Http\Requests\JenisPerolehan;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class UpdateStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status'  => 'required|in:active,inactive'
        ];
    }
}
