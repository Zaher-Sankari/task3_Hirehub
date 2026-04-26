<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OffensiveWords implements ValidationRule
{
    protected array $bannedWords = [
        'spam', 'offensive', 'hate', 'violence', 'stupid', 'idiot',
        'scam', 'fraud', 'fake', 'gambling', 'casino', 'porn',
        'sex', 'adult', 'drugs', 'cocaine', 'heroin'
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lowercaseValue = strtolower($value);
        
        foreach ($this->bannedWords as $word) {
            if (str_contains($lowercaseValue, $word)) {
                $fail("The {$attribute} contains offensive or inappropriate language.");
                return;
            }
        }
    }
    public function addWords(array $words): self
    {
        $this->bannedWords = array_merge($this->bannedWords, $words);
        return $this;
    }
}