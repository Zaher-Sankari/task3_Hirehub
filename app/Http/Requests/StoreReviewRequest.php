<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->type === 'client';    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id'=> 'required', 'exists:projects,id',
            'reviewable_type'=> 'required', 'in:project,user',
            'reviewable_id'=> 'required', 'integer',
            'rating'=>'required', 'integer', 'min:1', 'max:5',
            'comment'=> 'required', 'string', 'min:10'
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $project = Project::find($this->project_id);

            if (!$project) return;

            if ($project->client_id !== $this->user()->id) {
                $validator->errors()->add('project_id', 'You are not allowed to review this project.');
            }

            if ($project->status !== 'closed') {
                $validator->errors()->add('project_id', 'You can only review a project after it is closed.');
            }

            if ($this->reviewable_type === 'user') {
                $acceptedBid = $project->bids()->where('status', 'accepted')->first();
                
                if (!$acceptedBid || $acceptedBid->user_id != $this->reviewable_id) {
                    $validator->errors()->add('reviewable_id', 'You can only review the freelancer who worked on this project.');
                }
            }
        });
    }
}
