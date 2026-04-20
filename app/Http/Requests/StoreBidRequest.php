<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Rules\OffensiveWords;
use Illuminate\Foundation\Http\FormRequest;

class StoreBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        $project = Project::findOrFail($this->project_id);
        return $this->user()->type === 'freelancer' && $project->status === 'open';
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'proposal' => ['required', 'string', 'min:35', new OffensiveWords()],
            'delivery_days' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('proposal_letter')) {
            $this->merge([
                'proposal_letter' => trim($this->proposal_letter)
            ]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $project = $this->route('project');
            
            // Check if already proposed to this project
            if ($project && $project->proposals()->where('freelancer_id', $user->id)->exists()) {
                $validator->errors()->add('project_id', 'You have already submitted a proposal for this project.');
            }
            
            // Check if project is still open
            if ($project && $project->status !== 'open') {
                $validator->errors()->add('project_id', 'This project is no longer accepting proposals.');
            }

        });
    }
}
