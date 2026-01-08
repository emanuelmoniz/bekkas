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
            'address_id' => 'required_without:address_line_1|nullable|integer|exists:addresses,id',

            // New address fields (all required if address_line_1 is provided)
            'title' => 'required_without:address_id|string|max:255',
            'nif' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address_line_1' => 'required_without:address_id|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'postal_code' => 'required_without:address_id|string|max:20',
            'city' => 'required_without:address_id|string|max:100',
            'country_id' => 'required_without:address_id|exists:countries,id',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'address_id.required_without' => t('checkout.validation.address_required') ?: 'Please select an address or provide a new one.',
            'address_id.exists' => t('checkout.validation.address_invalid') ?: 'The selected address is invalid.',
            'address_id.integer' => t('checkout.validation.address_invalid') ?: 'The selected address is invalid.',
            
            'title.required_without' => t('checkout.validation.title_required') ?: 'Please provide an address title.',
            'title.string' => t('checkout.validation.title_required') ?: 'Please provide an address title.',
            'title.max' => t('checkout.validation.title_max') ?: 'Address title is too long.',
            
            'nif.string' => t('checkout.validation.nif_invalid') ?: 'NIF/VAT number format is invalid.',
            'nif.max' => t('checkout.validation.nif_max') ?: 'NIF/VAT number is too long.',
            
            'address_line_1.required_without' => t('checkout.validation.address_line_1_required') ?: 'Address is required.',
            'address_line_1.string' => t('checkout.validation.address_line_1_required') ?: 'Address is required.',
            'address_line_1.max' => t('checkout.validation.address_line_1_max') ?: 'Address is too long.',
            
            'address_line_2.string' => t('checkout.validation.address_line_2_invalid') ?: 'Address line 2 format is invalid.',
            'address_line_2.max' => t('checkout.validation.address_line_2_max') ?: 'Address line 2 is too long.',
            
            'postal_code.required_without' => t('checkout.validation.postal_code_required') ?: 'Postal code is required.',
            'postal_code.string' => t('checkout.validation.postal_code_required') ?: 'Postal code is required.',
            'postal_code.max' => t('checkout.validation.postal_code_max') ?: 'Postal code is too long.',
            
            'city.required_without' => t('checkout.validation.city_required') ?: 'City is required.',
            'city.string' => t('checkout.validation.city_required') ?: 'City is required.',
            'city.max' => t('checkout.validation.city_max') ?: 'City name is too long.',
            
            'country_id.required_without' => t('checkout.validation.country_required') ?: 'Country is required.',
            'country_id.exists' => t('checkout.validation.country_invalid') ?: 'Please select a valid country.',
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
        ]);
    }
}
