<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFreelancerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->type === 'freelancer';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bio' => 'nullable|string',
            'hourly_rate'=> 'nullable|numeric|min:0',
            'phone'=> 'nullable|string|max:20',
            'availability'=> 'nullable|in:available,busy,not available',
            'skills'=> 'nullable|array',
            'skills.*.id'=> 'required|exists:skills,id',
            'skills.*.experience' => 'required|integer|min:0',
        ];
    }
}
