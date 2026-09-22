<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:500', // Max 500 KB
            'primary_position' => 'required|string|max:10',
            'seniority' => 'nullable|string|max:50',
            'jersey_number' => 'nullable|integer',
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'date_of_birth' => 'nullable|date',
            'preferred_foot' => 'nullable|string|in:right,left,both',
            'coach_notes' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
        ];
    }
}