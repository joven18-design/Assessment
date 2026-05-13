<?php

namespace App\Http\Requests;

use App\Models\Issue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|min:10',
            'priority' => 'sometimes|string|in:' . implode(',', Issue::PRIORITIES),
            'category' => 'sometimes|string|in:' . implode(',', Issue::CATEGORIES),
            'status' => 'sometimes|string|in:' . implode(',', Issue::STATUSES),
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'Issue title must not exceed 255 characters.',
            'description.min' => 'Issue description must be at least 10 characters.',
            'priority.in' => 'Priority must be one of: ' . implode(', ', Issue::PRIORITIES),
            'category.in' => 'Category must be one of: ' . implode(', ', Issue::CATEGORIES),
            'status.in' => 'Status must be one of: ' . implode(', ', Issue::STATUSES),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
