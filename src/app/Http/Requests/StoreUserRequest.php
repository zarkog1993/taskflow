<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'player_profile' => 'nullable|array',
            'player_profile.jersey_number' => 'nullable|integer',
            'player_profile.primary_position' => 'nullable|string',
            'player_profile.preferred_foot' => 'nullable|string',
            'player_profile.category' => 'nullable|string',
            'player_profile.seniority' => 'nullable|string',
            'player_profile.fitness_status' => 'nullable|string',
        ];
    }
}
