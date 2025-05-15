<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLogRequest extends FormRequest
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
            'trace_id' => 'required|string|max:255',
            'timestamp' => 'required|date',
            'type' => [
                'required',
                'string',
                Rule::in(['request', 'db_orm', 'query', 'error', 'info', 'debug']),
                'max:50'
            ],
            'log_data' => 'required|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'timestamp.date_format' => 'The timestamp must be in ISO 8601 format (e.g., YYYY-MM-DDTHH:MM:SSZ).',
        ];
    }
}
