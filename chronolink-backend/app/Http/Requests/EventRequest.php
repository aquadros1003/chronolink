<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest as BaseRequest;

class EventRequest extends BaseRequest
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
            'title' => 'required|string',
            'location' => 'string|nullable',
            'description' => 'string|nullable',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'timeline_id' => 'required|exists:timelines,id',
            'label_id' => 'exists:labels,id',
        ];
    }
}
