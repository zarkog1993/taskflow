<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'club_name' => ['required', 'string', 'max:255'],
            'plan_type' => ['required', 'string', 'in:basic,pro,unlimited'],
        ];
    }
}