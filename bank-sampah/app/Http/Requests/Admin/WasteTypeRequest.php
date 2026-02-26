<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WasteTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Sesuaikan dengan gate/policy jika ada, untuk admin true cukup jika middleware auth menangani
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:waste_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:10'],
        ];
    }
}
