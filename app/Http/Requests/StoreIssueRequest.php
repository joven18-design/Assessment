<?php

namespace App\Http\Requests;

use App\Models\Issue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'priority' => 'required|string|in:' . implode(',', Issue::PRIORITIES),
            'category' => 'required|string|in:' . implode(',', Issue::CATEGORIES),
            'status' => 'sometimes|string|in:' . implode(',', Issue::STATUSES),
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Issue title is required.',
            'title.max' => 'Issue title must not exceed 255 characters.',
            'description.required' => 'Issue description is required.',
            'description.min' => 'Issue description must be at least 10 characters.',
            'priority.required' => 'Priority level is required.',
            'priority.in' => 'Priority must be one of: ' . implode(', ', Issue::PRIORITIES),
            'category.required' => 'Category is required.',
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
