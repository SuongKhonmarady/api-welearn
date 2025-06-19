<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScholarshipRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'required|string',
            'link' => 'sometimes|nullable|string|max:255',
            'official_link' => 'sometimes|nullable|string|max:255',
            'post_at' => 'required|date',
            'deadline' => 'sometimes|nullable|date',
            'eligibility' => 'sometimes|nullable|string',
            'host_country' => 'sometimes|nullable|string|max:255',
            'host_university' => 'sometimes|nullable|string|max:255',
            'program_duration' => 'sometimes|nullable|string|max:255',
            'degree_offered' => 'sometimes|nullable|string|max:255',
        ];
    }
}
