<?php

namespace App\Auth\Controllers;

use App\Auth\Models\Address;
use App\Auth\Services\AddressService;
use Exception;
use Illuminate\Http\Request;

class AddressController
{
    public function getAddresses()
    {
        try {
            $addresses = AddressService::getAddresss();
            return view('pages.user.dashboard.address', [
                'addresses' => $addresses
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createAddress(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'label' => 'nullable|string|max:100',
                'recipient_name' => 'required|string|max:150',
                'phone' => 'nullable|string|max:20',
                'street_address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'required|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'is_default_shipping' => 'boolean',
                'is_default_billing' => 'boolean',
            ], [
                'user_id.required' => 'User ID is required.',
                'user_id.exists' => 'The selected user does not exist.',
                'recipient_name.required' => 'Recipient name cannot be empty.',
                'recipient_name.max' => 'Recipient name is too long.',
                'street_address.required' => 'Street address is required.',
                'city.required' => 'City name is required.',
                'country.required' => 'Country field is required.',
                'latitude.numeric' => 'Latitude must be a valid number.',
                'longitude.numeric' => 'Longitude must be a valid number.',
            ]);

            $validated['is_default_shipping'] = $request->boolean('is_default_shipping', false);

            $validated['is_default_billing'] = $request->boolean('is_default_billing', false);

            AddressService::createAddress($validated);

            return redirect()->back()->with('success', 'Address is created successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateAddress(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'label' => 'nullable|string|max:100',
                'recipient_name' => 'required|string|max:150',
                'phone' => 'nullable|string|max:20',
                'street_address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',

                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',

                'is_default_shipping' => 'nullable|boolean',
                'is_default_billing' => 'nullable|boolean',
            ], [
                'recipient_name.required' => 'Recipient name cannot be empty.',
                'recipient_name.max' => 'Recipient name is too long.',
                'street_address.required' => 'Street address is required.',
                'city.required' => 'City name is required.',
                'country.required' => 'Country field is required.',
                'latitude.numeric' => 'Latitude must be a valid number.',
                'longitude.numeric' => 'Longitude must be a valid number.',
            ]);

            $validated['is_default_shipping'] = $request->boolean('is_default_shipping', false);

            $validated['is_default_billing'] = $request->boolean('is_default_billing', false);

            AddressService::updateAddress($id, $validated);

            return redirect()->back()->with('success', 'Address is updated successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteAddress($id)
    {
        try {
            $deleted = AddressService::deleteAddress(intval($id));
            if (!$deleted) {
                abort('500', 'Cannot delete address');
            }
            return redirect()->back()->with('success', 'Address is deleted');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
