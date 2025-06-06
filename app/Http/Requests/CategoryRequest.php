<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('categoria')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('categories', 'name')->ignore($categoryId)
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.string' => 'O nome da categoria deve ser um texto.',
            'name.max' => 'O nome da categoria não pode ter mais de 255 caracteres.',
            'name.min' => 'O nome da categoria deve ter pelo menos 2 caracteres.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'description' => 'descrição'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name),
            'description' => $this->description ? trim($this->description) : null
        ]);
    }
}