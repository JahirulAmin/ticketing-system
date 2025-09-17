<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'sometimes|in:open,closed,pending',
            'priority' => 'sometimes|in:low,medium,high',
            'category' => 'sometimes|in:technical,billing,general',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png|max:2048',  // 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The subject is required.',
            'title.max' => 'The subject may not be greater than 255 characters.',
            'description.required' => 'The description is required.',
            'status.in' => 'The selected status is invalid.',
            'priority.in' => 'The selected priority is invalid.',
            'category.in' => 'The selected category is invalid.',
            'attachment.file' => 'The attachment must be a file.',
            'attachment.mimes' => 'The attachment must be a file of type: pdf, jpg, png.',
            'attachment.max' => 'The attachment may not be greater than 2MB.',
        ];
    }
}
