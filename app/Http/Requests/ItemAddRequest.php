<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ItemAddRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'publish' => 'boolean',
            'expiry' => 'required|date',
            'status' => 'required|string|max:255',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException( response()->json([
            "success" => false,
            "message" => "validation error",
            "error" => $validator->errors()
        ]));
    }
}
