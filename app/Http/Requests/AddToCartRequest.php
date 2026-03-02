<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public can add to cart
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            // options is optional at the request level; completeness is enforced
            // in the controller after loading the product's option types.
            'options' => ['sometimes', 'nullable', 'array'],
            // Individual option id validity is enforced in the controller
            // after loading the product's option types; no strict rule here
            // prevents blank/null values from generating raw validation.integer messages.
            'options.*' => ['nullable'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 999 units.',
        ];
    }

    /**
     * Always return JSON for AJAX / fetch requests so the caller
     * receives a consistent {success, message} payload instead of
     * an HTML redirect that would cause a SyntaxError on the client.
     */
    protected function failedValidation(Validator $validator): never
    {
        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
