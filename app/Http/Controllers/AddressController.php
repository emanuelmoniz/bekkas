<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'nif' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country_id' => 'required|exists:countries,id',
            'is_default' => 'nullable|boolean',
        ], [
            'title.required' => t('validation.title_required') ?: 'Please enter an address title.',
            'address_line_1.required' => t('validation.address_required') ?: 'Please enter your address.',
            'postal_code.required' => t('validation.postal_code_required') ?: 'Please enter your postal code.',
            'city.required' => t('validation.city_required') ?: 'Please enter your city.',
            'country_id.required' => t('validation.country_required') ?: 'Please select your country.',
        ]);

        $user = Auth::user();

        // If this is the first address, force default
        if ($user->addresses()->count() === 0) {
            $data['is_default'] = true;
        }

        if (! empty($data['is_default'])) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            ...$data,
            'is_default' => ! empty($data['is_default']),
        ]);

        return redirect()->route('profile.edit')->with('success', t('profile.address_added_success') ?: 'Address added successfully!');
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'nif' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country_id' => 'required|exists:countries,id',
            'is_default' => 'nullable|boolean',
        ], [
            'title.required' => t('validation.title_required') ?: 'Please enter an address title.',
            'address_line_1.required' => t('validation.address_required') ?: 'Please enter your address.',
            'postal_code.required' => t('validation.postal_code_required') ?: 'Please enter your postal code.',
            'city.required' => t('validation.city_required') ?: 'Please enter your city.',
            'country_id.required' => t('validation.country_required') ?: 'Please select your country.',
        ]);

        $user = $address->user;

        // If marking this address as default, unmark all others first
        if ($request->filled('is_default') && $request->boolean('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('profile.edit')->with('success', t('profile.address_updated_success') ?: 'Address updated successfully!');
    }

    public function destroy(Address $address)
    {
        $this->authorizeAddress($address);

        $user = $address->user;
        $wasDefault = $address->is_default;

        $address->delete();

        // If default was deleted, promote another address
        if ($wasDefault) {
            $first = $user->addresses()->first();
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }

        return redirect()->route('profile.edit')->with('success', t('profile.address_deleted_success') ?: 'Address deleted successfully!');
    }

    protected function authorizeAddress(Address $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
