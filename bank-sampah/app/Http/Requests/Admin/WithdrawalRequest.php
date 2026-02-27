<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'method' => ['required', 'in:CASH,TRANSFER'],
            'bank_name' => ['nullable', 'required_if:method,TRANSFER', 'string', 'max:255'],
            'account_number' => ['nullable', 'required_if:method,TRANSFER', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Penarikan minimal Rp 1.000.',
            'bank_name.required_if' => 'Nama Bank wajib diisi jika metode Transfer.',
            'account_number.required_if' => 'Nomor Rekening wajib diisi jika metode Transfer.',
        ];
    }
}
