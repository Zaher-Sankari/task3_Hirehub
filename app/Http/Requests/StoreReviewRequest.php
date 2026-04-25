<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Rules\OffensiveWords;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'project_id' => ['required', 'exists:projects,id'],
            'reviewable_type' => ['required', 'string', 'in:project,freelancer'],
            'reviewable_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', new OffensiveWords()],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Project ID is required.',
            'project_id.exists' => 'The specified project does not exist.',
            'reviewable_type.required' => 'Review type is required.',
            'reviewable_type.in' => 'Review type must be either "project" or "freelancer".',
            'reviewable_id.required' => 'Review target ID is required.',
            'rating.required' => 'Rating is required.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',
            'comment.required' => 'Review comment is required.',
            'comment.min' => 'Comment must be at least 10 characters.',
        ];
    }

    /**
     * Prepare data for validation.
     */
    public function prepareForValidation(): void
    {
        if ($this->has('comment')) {
            $this->merge([
                'comment' => trim($this->comment)
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
            $project = Project::find($this->project_id);

            if (!$project) {
                $validator->errors()->add('project_id', 'Project not found.');
                return;
            }

// Only the client who owns the project can review
            if ($project->client_id !== $user->id) {
                $validator->errors()->add('project_id', 'You are not authorized to review this project.');
                return;
            }

// Project must be closed before reviewing
            if ($project->status !== 'closed') {
                $validator->errors()->add('project_id', 'You can only review a project after it is closed.');
                return;
            }

// Prevent duplicate reviews
            $existingReview = $project->reviews()
                ->where('reviewable_type', 'like', '%' . $this->reviewable_type)
                ->where('reviewable_id', $this->reviewable_id)
                ->exists();

            if ($existingReview) {
                $validator->errors()->add('reviewable_id', 'You have already reviewed this.');
                return;
            }

// If reviewing a freelancer, verify they worked on this project
            if ($this->reviewable_type === 'freelancer') {
                $acceptedBid = $project->bids()
                    ->where('status', 'accepted')
                    ->first();

                if (!$acceptedBid || $acceptedBid->freelancer_id != $this->reviewable_id) {
                    $validator->errors()->add('reviewable_id', 'You can only review the freelancer who worked on this project.');
                }
            }
        });
    }
}
