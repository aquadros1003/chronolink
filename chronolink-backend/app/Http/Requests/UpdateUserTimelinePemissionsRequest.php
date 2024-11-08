<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest as BaseRequest;

class UpdateUserTimelinePemissionsRequest extends BaseRequest
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
            'permissions' => 'required|array',
            'permissions.*' => 'uuid|exists:permissions,id',
            'user_email' => 'required|email|exists:users,email',
        ];
    }
}
