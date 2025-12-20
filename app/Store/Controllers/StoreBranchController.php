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

    public function viewAdminStoreBranchListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query', null);

            $query = StoreBranch::query();

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

            $branches = $query->paginate($perPage);
            $branches->appends(request()->query());

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

    public function deleteSelectedStoreBranches(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No store branches selected for deletion.');
            }

            $store_branches = StoreBranch::whereIn('id', $ids)->get();

            foreach ($store_branches as $branch) {
                $branch->delete();
            }

            return redirect()->back()->with('success', 'Selected store branches deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected store branches.");
        }
    }


    public function deleteAllStoreBranches()
    {
        try {
            $store_branches = StoreBranch::all();

            foreach ($store_branches as $branch) {
                $branch->delete();
            }

            return redirect()->back()->with('success', 'All store branches deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all store branches.");
        }
    }
}
