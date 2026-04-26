<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isFreelancer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'skills' => ['sometimes', 'array'],
            'skills.*.id' => ['sometimes', 'exists:skills,id'],
            'skills.*.experience' => ['sometimes', 'integer', 'min:0', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'skills.required' => 'The skills list is required.',
            'skills.array' => 'The skills must be provided as an array.',
            'skills.*.id.required' => 'Each skill must have a valid ID.',
            'skills.*.id.exists' => 'One or more selected skills do not exist in our database.',
            'skills.*.experience.required' => 'Years of experience is required for each skill.',
            'skills.*.experience.integer' => 'Years of experience must be a whole number.',
            'skills.*.experience.min' => 'Years of experience cannot be negative.',
            'skills.*.experience.max' => 'Years of experience cannot exceed 50 years.',
        ];
    }
}
