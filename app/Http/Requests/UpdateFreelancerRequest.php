<?php

namespace App\Http\Requests;

use App\Rules\OffensiveWords;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFreelancerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->type === 'freelancer';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'bio' => ['nullable', 'string', 'max:200', new OffensiveWords()],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+\d\s\-\(\)]+$/'],
            'availability' => ['nullable', 'string', 'in:available,busy,not_available'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'portfolio_links' => ['nullable', 'array', 'max:10'],
            'portfolio_links.*' => ['url', 'max:255'],
            'skills' => ['nullable', 'array'],
            'skills.*.id' => ['required', 'exists:skills,id'],
            'skills.*.experience' => ['required', 'integer', 'min:0', 'max:50'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'bio.max' => 'Bio cannot exceed 200 characters.',
            'hourly_rate.min' => 'Hourly rate cannot be negative.',
            'hourly_rate.max' => 'Hourly rate cannot exceed $1000.',
            'phone.regex' => 'Please enter a valid phone number.',
            'availability.in' => 'Availability must be: available, busy, or not_available.',
            'profile_picture.image' => 'Profile picture must be an image file.',
            'profile_picture.max' => 'Profile picture cannot exceed 2MB.',
            'portfolio_links.max' => 'You can add up to 10 portfolio links.',
            'portfolio_links.*.url' => 'Each portfolio link must be a valid URL.',
            'skills.*.id.exists' => 'Selected skill does not exist.',
            'skills.*.experience.min' => 'Years of experience cannot be negative.',
            'skills.*.experience.max' => 'Years of experience cannot exceed 50.',
        ];
    }

    /**
     * Prepare data for validation.
     */
    public function prepareForValidation(): void
    {
        if ($this->has('bio')) {
            $this->merge(['bio' => trim($this->bio)]);
        }
        
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/[^0-9+]/', '', $this->phone)]);
        }
    }
}