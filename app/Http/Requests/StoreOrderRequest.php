<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Either address_id OR new address fields required
            'address_id' => 'required_without:address_line_1|integer|exists:addresses,id',

            // New address fields (all required if address_line_1 is provided)
            'title' => 'required_with:address_line_1|string|max:255',
            'nif' => 'required_with:address_line_1|string|max:50',
            'address_line_1' => 'required_without:address_id|string|max:255',
            'address_line_2' => 'required_with:address_line_1|string|max:255',
            'postal_code' => 'required_with:address_line_1|string|max:20',
            'city' => 'required_with:address_line_1|string|max:100',
            'country' => 'required_with:address_line_1|string|max:100',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'address_id.required_without' => 'Please select an address or provide a new one.',
            'address_id.exists' => 'The selected address is invalid.',
            'title.required_with' => 'Please provide an address title.',
            'nif.required_with' => 'NIF/VAT number is required.',
            'address_line_1.required_without' => 'Address is required.',
            'address_line_2.required_with' => 'Address details are required.',
            'postal_code.required_with' => 'Postal code is required.',
            'city.required_with' => 'City is required.',
            'country.required_with' => 'Country is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from string inputs
        $this->merge([
            'address_line_1' => trim($this->address_line_1),
            'address_line_2' => trim($this->address_line_2),
            'city' => trim($this->city),
            'country' => trim($this->country),
        ]);
    }
}
