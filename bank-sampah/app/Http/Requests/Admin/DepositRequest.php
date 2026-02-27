<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.waste_type_id' => ['required', 'exists:waste_types,id'],
            'items.*.weight' => ['required', 'numeric', 'min:0.1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Daftar item sampah tidak boleh kosong.',
            'items.*.weight.min' => 'Berat minimal :min kg.',
        ];
    }
}
