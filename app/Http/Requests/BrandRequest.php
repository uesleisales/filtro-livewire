<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('marca')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('brands', 'name')->ignore($brandId)
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'logo' => [
                'nullable',
                'string',
                'max:500',
                'url'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da marca é obrigatório.',
            'name.string' => 'O nome da marca deve ser um texto.',
            'name.max' => 'O nome da marca não pode ter mais de 255 caracteres.',
            'name.min' => 'O nome da marca deve ter pelo menos 2 caracteres.',
            'name.unique' => 'Já existe uma marca com este nome.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'logo.string' => 'O logo deve ser um texto.',
            'logo.max' => 'O logo não pode ter mais de 500 caracteres.',
            'logo.url' => 'O logo deve ser uma URL válida.'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'description' => 'descrição',
            'logo' => 'logo'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name),
            'description' => $this->description ? trim($this->description) : null,
            'logo' => $this->logo ? trim($this->logo) : null
        ]);
    }
}