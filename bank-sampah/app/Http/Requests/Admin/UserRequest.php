<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('customer') ?? $this->route('id');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $isUpdate ? Rule::unique('users')->ignore($userId) : Rule::unique('users'),
                'regex:/^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,7}$/',
            ],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:6'],
            'phone' => ['required', 'numeric', 'digits_between:10,12'],
            'role' => ['required', 'in:admin,nasabah,PETUGAS,ADMIN,NASABAH'],
            'address' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.regex' => 'Format email tidak valid (harus mengandung domain yang benar, contoh: .com).',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal :min karakter.',
            'phone.required' => 'No. Telepon wajib diisi.',
            'phone.numeric' => 'No. Telepon harus berupa angka.',
            'phone.digits_between' => 'No. Telepon harus antara 10 sampai 12 digit.',
        ];
    }
}
