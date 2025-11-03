<?php

namespace App\Store\Controllers;

use App\Store\Models\StoreBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Cache;

class StoreBranchController
{
    public function viewUserStoreLocatorPage(Request $request)
    {
        try {
            $page = request()->get('page', 1);
            $perPage = 20;
            $cacheKey = "branches_page_{$page}_per_{$perPage}";

            $branches = Cache::remember($cacheKey, config('app.cache_time', 3600), function () use ($perPage) {
                $paginator = StoreBranch::orderBy('name', 'asc')->paginate($perPage);

                $paginator->getCollection()->transform(fn($branch) => $branch->jsonResponse());

                return $paginator;
            });

            if ($request->expectsJson()) {
                return response()->json($branches);
            }

            return view('pages.user.core.store_locator', [
                'branches' => $branches,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminStoreBranchListPage()
    {
        try {
            $branches = StoreBranch::orderBy('name', 'asc')
                ->paginate(10);

            $branches->getCollection()->transform(function ($branch) {
                return $branch->jsonResponse();
            });

            return view('pages.admin.dashboard.store_branch.store_branch_list', [
                'branches' => $branches,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createStoreBranch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'country' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'open_time' => 'nullable',
                'close_time' => 'nullable',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            $validated = $validator->validated();

            StoreBranch::create($validated);

            return redirect()->back()->with('success', 'Store branch created successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateStoreBranch(Request $request, $id)
    {
        try {
            $branch = StoreBranch::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'country' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'open_time' => 'nullable',
                'close_time' => 'nullable',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            $validated = $validator->validated();

            $branch->fill($validated);
            $branch->save();

            return redirect()->back()->with('success', 'Store branch updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteStoreBranch($id)
    {
        try {
            $branch = StoreBranch::findOrFail($id);
            $branch->delete();

            return redirect()->back()->with('success', 'Store branch deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
