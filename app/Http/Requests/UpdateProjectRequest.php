<?php

namespace App\Http\Requests;

use App\Rules\OffensiveWords;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');
        $user = $this->user();

        return $user && $user->type === 'client' && $user->id === $project->client_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['sometimes', 'string', 'min:10', 'max:255', new OffensiveWords()],
            'description' => ['sometimes', 'string', 'min:50', new OffensiveWords()],
            'budget_type' => ['sometimes', Rule::in(['fixed', 'hourly'])],
            'deadline' => ['sometimes', 'date', 'after:today'],
            'tags' => ['nullable', 'array', 'max:5'],
            'tags.*' => ['exists:tags,id'],
            'status' => ['sometimes', Rule::in(['open', 'closed'])]
        ];
        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.min' => 'Title must be at least 10 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.min' => 'Description must be at least 50 characters.',
            'budget.min' => 'Budget must be at least :min.',
            'budget.max' => 'Hourly rate cannot exceed $500 per hour.',
            'budget_type.in' => 'Budget type must be fixed or hourly.',
            'deadline.after' => 'Deadline must be in the future.',
            'tags.max' => 'You can add up to 5 tags only.',
            'tags.*.exists' => 'One or more selected tags are invalid.',
            'status.in' => 'Status must be open or closed.',
        ];
    }

    /**
     * Prepare data for validation.
     */
    public function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge(['title' => trim($this->title)]);
        }

        if ($this->has('description')) {
            $this->merge(['description' => trim($this->description)]);
        }
    }
}
