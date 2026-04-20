<?php

namespace App\Http\Requests;

use App\Rules\OffensiveWords;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->type === 'client';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:10', 'max:255', new OffensiveWords()],
            'description' => ['required', 'string', 'min:50', new OffensiveWords()],
            'budget_type' => ['required', Rule::in(['fixed', 'hourly'])],
            'budget' => ['required', 'numeric', 'min:1'],  
            'deadline' => ['required', 'date', 'after:today'], 
            'tags' => ['nullable', 'array', 'max:5'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required.',
            'title.min' => 'Title must be at least 10 characters.',
            'description.required' => 'Project description is required.',
            'description.min' => 'Description must be at least 50 characters.',
            'budget_type.required' => 'Budget type is required.',
            'budget_type.in' => 'Budget type must be fixed or hourly.',
            'budget.required' => 'Budget amount is required.',
            'budget.min' => 'Budget must be at least 1.',
            'deadline.required' => 'Deadline is required.',
            'deadline.after' => 'Deadline must be in the future.',
            'tags.max' => 'You can add up to 5 tags only.',
        ];
    }

    /**
     * Prepare data for validation.
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim($this->title),
            'description' => trim($this->description),
        ]);
    }
}