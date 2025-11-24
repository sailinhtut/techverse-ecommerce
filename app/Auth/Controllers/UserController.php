<?php

namespace App\Auth\Controllers;

use App\Auth\Models\Permission;
use App\Auth\Models\User;
use App\Auth\Models\UserRole;
use App\Auth\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController
{
    public function showProfile()
    {
        return view('pages.user.dashboard.profile');
    }

    public function viewAdminUserListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = User::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            switch ($sortBy) {
                case 'last_updated':
                    $query->orderBy('updated_at', $orderBy)
                        ->orderBy('id', $orderBy);
                    break;

                case 'last_created':
                    $query->orderBy('created_at', $orderBy)->orderBy('id', $orderBy);
                    break;

                default:
                    $query->orderBy('updated_at', 'desc')
                        ->orderBy('id', 'desc');
            }

            $users = $query->paginate($perPage);
            $users->appends(request()->query());

            $users->getCollection()->transform(function ($user) {
                return $user->jsonResponse(['role']);
            });

            $roles = UserRole::all();
            $roles = $roles->map(fn($role) => $role->jsonResponse());


            return view('pages.admin.dashboard.user.user_list', [
                'roles' => $roles,
                'users' => $users
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminRoleListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = UserRole::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            switch ($sortBy) {
                case 'last_updated':
                    $query->orderBy('updated_at', $orderBy)
                        ->orderBy('id', $orderBy);
                    break;

                case 'last_created':
                    $query->orderBy('created_at', $orderBy)->orderBy('id', $orderBy);
                    break;

                default:
                    $query->orderBy('updated_at', 'desc')
                        ->orderBy('id', 'desc');
            }

            $roles = $query->paginate($perPage);
            $roles->appends(request()->query());
            $roles->getCollection()->transform(function ($role) {
                return $role->jsonResponse();
            });

            $permissions = Permission::all();
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
            $permissions = Permission::orderBy('id', 'asc')->get();

            $permissions = $permissions->map(fn($permission) => $permission->jsonResponse());

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

    public function createUserAdmin(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'phone_one' => 'nullable|numeric|digits_between:7,15',
                'phone_two' => 'nullable|numeric|digits_between:7,15',
                'role_id' => 'required|exists:user_roles,id',
                'date_of_birth' => 'nullable|date',
                'profile' => 'nullable|image|max:2048',
            ]);

            $created_user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone_one' => $validated['phone_one'],
                'phone_two' => $validated['phone_two'],
                'role_id' => $validated['role_id'],
                'date_of_birth' => $validated['date_of_birth'],
            ]);


            if ($request->hasFile('profile')) {
                $created_user->profile = Storage::disk('public')
                    ->putFile('users/profiles',  $request->file('profile'));
            }

            $created_user->save();

            return back()->with('success', 'User Created Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateUserAdmin(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string',
                'email' => 'nullable|email|unique:users,email,' . $id,
                'password' => 'nullable|min:6',
                'phone_one' => 'nullable|numeric|digits_between:7,15',
                'phone_two' => 'nullable|numeric|digits_between:7,15',
                'role_id' => 'nullable|exists:user_roles,id',
                'date_of_birth' => 'nullable|date',
                'profile' => 'nullable|image|max:2048',
                'remove_profile' => 'nullable|boolean',
            ]);

            $user = User::findOrFail($id);

            $user->fill([
                'name' => $validated['name'] ?? $user->name,
                'email' => $validated['email'] ?? $user->email,
                'phone_one' => $validated['phone_one'] ?? $user->phone_one,
                'phone_two' => $validated['phone_two'] ?? $user->phone_two,
                'role_id' => $validated['role_id'] ?? $user->role_id,
                'date_of_birth' => $validated['date_of_birth'] ?? $user->date_of_birth,
            ]);


            if ($request->has('remove_profile') && $request->boolean('remove_profile', false) && !is_null($user->profile)) {
                Storage::disk('public')->delete($user->profile);
                $user->profile = null;
            }

            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                    Storage::disk('public')->delete($user->profile);
                }
                $user->profile = Storage::disk('public')
                    ->putFile('users/profiles',  $request->file('profile'));
            }

            $passwordChanged = false;
            if (!empty($validated['password'])) {
                $passwordChanged = true;
                $user->password = $validated['password'];
            }

            $user->save();

            if ($passwordChanged) {
                DB::table('users')->where('id', $user->id)->update(['remember_token' => null]);
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            return back()->with('success', 'User Updated Successfully');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteUserAdmin(Request $request, string $id)
    {
        try {
            $deleted = UserService::deleteUser($id);

            if (!$deleted) throw new Exception('Cannot Delete User');

            DB::table('sessions')->where('user_id', $id)->delete();

            return back()->with('success', 'User Deleted Successfully');
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


    public function createRole(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:user_roles,name',
                'display_name' => 'required|string',
                'description' => 'nullable|string',
                'is_company_member' => 'nullable|boolean',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
            ]);

            $permissions = $validated['permissions'] ?? [];

            $role = UserRole::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'is_company_member' => $validated['is_company_member'] ?? false,
                'description' => $validated['description'] ?? null,
                'permissions' => $permissions,
            ]);

            return back()->with('success', 'Role created successfully: ' . $role->display_name);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateRole(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'display_name' => 'required|string',
                'description' => 'nullable|string',
                'permissions' => 'nullable|array',
                'is_company_member' => 'nullable|boolean',
                'permissions.*' => 'string|exists:permissions,name',
            ]);

            $role = UserRole::findOrFail($id);

            $role->update([
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
                'is_company_member' => $validated['is_company_member'] ?? false,
                'permissions' => $validated['permissions'] ?? [],
            ]);

            return back()->with('success', 'Role updated successfully: ' . $role->display_name);
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

    public function deleteSelectedRoles(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No roles selected for deletion.');
            }

            $roles = UserRole::whereIn('id', $ids)->get();

            foreach ($roles as $role) {
                $role->delete();
            }

            return redirect()->back()->with('success', 'Selected roles deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected roles.");
        }
    }

    public function deleteAllRoles()
    {
        try {
            $roles = UserRole::all();

            foreach ($roles as $role) {
                $role->delete();
            }

            return redirect()->back()->with('success', 'All roles deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all roles.");
        }
    }

    public function deleteSelectedUsers(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No users selected for deletion.');
            }

            $users = User::whereIn('id', $ids)->get();

            foreach ($users as $user) {
                if ($user->profile) {
                    Storage::disk('public')->delete($user->profile);
                }
                $user->delete();
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            return redirect()->back()->with('success', 'Selected users deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected users.");
        }
    }


    public function deleteAllUsers()
    {
        try {
            $users = User::all();

            foreach ($users as $user) {
                if ($user->profile) {
                    Storage::disk('public')->delete($user->profile);
                }
                $user->delete();
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            return redirect()->back()->with('success', 'All users deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all users.");
        }
    }
}
