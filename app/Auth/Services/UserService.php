<?php

namespace App\Auth\Services;

use App\Auth\Models\Notification;
use App\Auth\Models\User;
use Exception;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isNull;

class UserService
{
    public static function getProfile($id): User
    {
        try {
            $user = User::find($id);
            if (!$user) throw new Exception('No User Found');

            return $user;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function createUser($values): User
    {
        try {
            $user = User::create($values);

            Notification::create([
                'user_id' => $user->id,
                'image' => url(asset('assets/images/computer_promotion.png')),
                'title' => 'Welcome to ' . config('app.name'),
                'message' => 'Get start-up promotions and explore best products'
            ]);

            return $user;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function updateUser($id, $values): User
    {
        try {
            $user = User::find($id);

            if (!$user) throw new Exception('No User Found');

            if (array_key_exists('remove_profile', $values) && $values['remove_profile'] && !isNull($user->profile) && Storage::disk('public')->exists($user->profile)) {
                Storage::disk('public')->delete($user->profile);
                $user->profile = null;
            }

            if (array_key_exists('profile', $values)) {
                if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                    Storage::disk('public')->delete($user->profile);
                }
                $user->profile = Storage::disk('public')
                    ->putFile('users/profiles', $values['profile']);
            }

            $user->fill([
                'name' => $values['name'] ?? $user->name,
                'phone_one' => $values['phone_one'] ?? $user->phone_one,
                'phone_two' => $values['phone_two'] ?? $user->phone_two,
            ]);

            $user->save();
            return $user;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    public static function deleteUser($id): bool
    {
        try {
            $user = User::find($id);

            if (!$user) throw new Exception('No User Found');

            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }

            $user->delete();


            return true;
        } catch (Exception $e) {
            throw ($e);
        }
    }
}
