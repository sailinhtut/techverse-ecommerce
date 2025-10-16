<?php

namespace App\Auth\Controllers;

use App\Auth\Models\Permission;
use App\Auth\Models\User;
use App\Auth\Models\UserRole;
use App\Auth\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController
{

    public function showProfile()
    {
        return view('pages.user.dashboard.profile');
    }

    public function viewAdminUserListPage()
    {
        try {
            $users = User::orderBy('id', 'desc')->paginate(10);

            $users->getCollection()->transform(function ($user) {
                return $user->jsonResponse(['role']);
            });

            return view('pages.admin.dashboard.user.user_list', [
                'users' => $users
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminRoleListPage()
    {
        try {
            $roles = UserRole::orderBy('id', 'desc')->paginate(10);

            $permissions = Permission::all();

            $roles->getCollection()->transform(function ($role) {
                return $role->jsonResponse();
            });

            $permissions = $permissions->map(fn($permission) => $permission->jsonResponse());

            return view('pages.admin.dashboard.user.role_list', [
                'roles' => $roles,
                'permissions' => $permissions
            ]);
        } catch (Exception $e) {
            throw $e;
            return handleErrors($e);
        }
    }

    public function viewAdminPermissionListPage()
    {
        try {
            $permissions = Permission::orderBy('id', 'desc')->paginate(10);

            $permissions->getCollection()->transform(function ($permission) {
                return $permission->jsonResponse();
            });

            return view('pages.admin.dashboard.user.permission_list', [
                'permissions' => $permissions,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }



    public function getProfile(Request $request, string $id)
    {
        try {
            $user = UserService::getProfile($id);

            return $user->jsonResponse();
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateProfile(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string',
                'phone_one' => 'nullable|numeric|digits_between:7,15',
                'phone_two' => 'nullable|numeric|digits_between:7,15',
                'profile' => 'nullable|image|max:2048',
                'remove_profile' => 'nullable|boolean',
            ], [
                'name.string' => 'Name must be text letter',
                'phone_one.numeric' => 'Phone 1 must be numeric values',
                'phone_two.numeric' => 'Phone 2 must be numeric values',
                'phone_one.digits_between' => 'Phone 1 must be between 7 and 15 digits',
                'phone_two.digits_between' => 'Phone 2 must be between 7 and 15 digits'
            ]);

            if ($request->has('remove_profile')) {
                $validated['remove_profile'] = $request->boolean('remove_profile', false);
            }

            if ($request->hasFile('profile')) {
                $validated['profile'] = $request->file('profile');
            }

            $updated_user = UserService::updateUser($id, $validated);

            return back()->with('success', 'Account Updated Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateRole(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
            ], [
                'permissions.array' => 'Permissions must be sent as an array.',
                'permissions.*.exists' => 'One or more selected permissions are invalid.',
            ]);

            $role = UserRole::findOrFail($id);

            // Use empty array if no permissions are provided
            $permissions = $validated['permissions'] ?? [];

            $role->update([
                'permissions' => $permissions,
            ]);

            return back()->with('success', 'Permissions updated successfully for role: ' . $role->display_name);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function deleteProfile(Request $request, string $id)
    {
        try {
            $deleted = UserService::deleteUser($id);

            if (!$deleted) throw new Exception('Cannot Delete User');

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('shop.get')->with('success', 'Account Deleted Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteRole($id)
    {
        try {
            $role = UserRole::findOrFail($id);
            $role->delete();

            return redirect()->back()->with('success', 'Role deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
