<?php

namespace App\Http\Requests;

use App\Rules\OffensiveWords;
use Illuminate\Foundation\Http\FormRequest;

class StoreBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $project = $this->route('project');

        if (!$user || !$project) {
            return false;
        }

        // Only freelancers can bid, and project must be open
        return $user->type === 'freelancer' && $project->status === 'open';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'proposal' => ['required', 'string', 'min:35', new OffensiveWords()],
            'delivery_days' => ['required', 'integer', 'min:1', 'max:90'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Bid amount is required.',
            'amount.min' => 'Bid amount must be at least $1.',
            'proposal.required' => 'Proposal letter is required.',
            'proposal.min' => 'Proposal must be at least 35 characters.',
            'delivery_days.required' => 'Delivery days are required.',
            'delivery_days.min' => 'Delivery days must be at least 1 day.',
            'delivery_days.max' => 'Delivery days cannot exceed 90 days.',
        ];
    }

    /**
     * Prepare data for validation.
     */
    public function prepareForValidation(): void
    {
        if ($this->has('proposal')) {
            $this->merge([
                'proposal' => trim($this->proposal)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $project = $this->route('project');

            if (!$project) {
                $validator->errors()->add('project_id', 'Project not found.');
                return;
            }

// Check if freelancer already submitted a bid for this project
            if ($project->bids()->where('freelancer_id', $user->id)->exists()) {
                $validator->errors()->add('project_id', 'You have already submitted a proposal for this project.');
            }

// Double-check project is still open
            if ($project->status !== 'open') {
                $validator->errors()->add('project_id', 'This project is no longer accepting proposals.');
            }
        });
    }
}
