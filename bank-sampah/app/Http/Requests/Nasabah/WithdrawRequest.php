<?php

namespace App\Http\Requests\Nasabah;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'method' => 'required|in:CASH,TRANSFER',
            'amount' => 'required|numeric|min:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Jumlah penarikan minimal Rp. 10.000',
            'amount.numeric' => 'Jumlah penarikan minimal Rp. 10.000',
            'amount.min' => 'Jumlah penarikan minimal Rp. 10.000',
        ];
    }
}
