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
        $rules = [
            'title' => ['required', 'string', 'min:10', 'max:255', new OffensiveWords()],
            'description' => ['required', 'string', 'min:25', new OffensiveWords()],
            'budget_type' => ['required', Rule::in(['fixed', 'hourly'])],
            'deadline' => ['required', 'date', 'after:today'],
            'tags' => ['nullable', 'array', 'max:5'],
            'tags.*' => ['exists:tags,id'],
        ];

        if ($this->budget_type === 'fixed') {
            $rules['budget'] = ['required', 'numeric', 'min:10'];
        } else {
            $rules['budget'] = ['required', 'numeric', 'min:5', 'max:200'];
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required.',
            'title.min' => 'Title must be at least 10 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.required' => 'Project description is required.',
            'description.min' => 'Description must be at least 25 characters.',
            'budget_type.required' => 'Please specify budget type (fixed or hourly).',
            'budget_type.in' => 'Budget type must be either fixed or hourly.',
            'budget.required' => 'Budget amount is required.',
            'budget.min' => 'Budget must be at least :min.',
            'budget.max' => 'Hourly rate cannot exceed $200 per hour.',
            'deadline.required' => 'Project deadline is required.',
            'deadline.after' => 'Deadline must be in the future.',
            'tags.max' => 'You can add up to 5 tags only.',
            'tags.*.exists' => 'One or more selected tags are invalid.',
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
