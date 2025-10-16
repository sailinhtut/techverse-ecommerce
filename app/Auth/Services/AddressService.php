<?php

namespace App\Auth\Services;

use App\Auth\Models\Address;
use Exception;
use Illuminate\Http\Request;

class AddressService
{
    public static function getAddresss()
    {
        try {
            if (!auth()->check()) abort(403, 'Unauthenticated');
            
            $addresses = Address::where('user_id',auth()->id())->orderBy('id', 'desc')->paginate(20);
            $addresses->getCollection()->transform(function ($address) {
                return $address->jsonResponse();
            });
            return $addresses;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function createAddress(array $data)
    {
        try {
            $address = Address::create($data);

            if ($data['is_default_shipping']) {
                Address::where('user_id', $data['user_id'])
                    ->where('id', '!=', $address->id)
                    ->update(['is_default_shipping' => false]);
            }

            if ($data['is_default_billing']) {
                Address::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default_billing' => false]);
            }
            return $address;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function updateAddress(int $id, array $data)
    {
        try {
            $address = Address::find($id);

            if (!$address) {
                abort(404, 'No address found');
            }

            $address->update([
                'label' => $data['label'] ?? $address->label,
                'recipient_name' => $data['recipient_name'] ?? $address->recipient_name,
                'phone' => $data['phone'] ?? $address->phone,
                'street_address' => $data['street_address'] ?? $address->street_address,
                'city' => $data['city'] ?? $address->city,
                'state' => $data['state'] ?? $address->state,
                'postal_code' => $data['postal_code'] ?? $address->postal_code,
                'country' => $data['country'] ?? $address->country,
                'latitude' => $data['latitude'] ?? $address->latitude,
                'longitude' => $data['longitude'] ?? $address->longitude,
                'is_default_shipping' => $data['is_default_shipping'] ?? $address->is_default_shipping,
                'is_default_billing' => $data['is_default_billing'] ??  $address->is_default_billing,
            ]);

            if ($data['is_default_shipping']) {
                Address::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default_shipping' => false]);
            }

            if ($data['is_default_billing']) {
                Address::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default_billing' => false]);
            }
            $address->save();
            return $address;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function deleteAddress(int $id)
    {
        try {
            $address = Address::find($id);
            if (!$address) abort(404, 'No address found');
            $address->delete();
            return true;
        } catch (Exception $e) {
            throw ($e);
        }
    }
}
