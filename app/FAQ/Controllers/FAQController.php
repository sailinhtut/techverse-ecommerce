<?php

namespace App\FAQ\Controllers;

use App\FAQ\Models\FAQ;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FAQController
{
    public function viewFAQPage(Request $request)
    {
        try {
            $faqs = FAQ::active()
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            return view('pages.user.core.frequent_questions', [
                'faqs' => $faqs,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function viewAdminFAQListPage(Request $request)
    {
        try {
            $sortBy = $request->get('sortBy', 'sort_order');
            $orderBy = $request->get('orderBy', 'asc');
            $perPage = $request->get('perPage', 20);
            $search = $request->get('query');

            $query = FAQ::query();

            if ($search) {
                $query->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
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

            $faqs = $query->paginate($perPage);
            $faqs->appends($request->query());

            $faqs->getCollection()->transform(
                fn($faq) => $faq->jsonResponse()
            );

            return view('pages.admin.dashboard.faq.faq_list', [
                'faqs' => $faqs,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createFAQ(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            FAQ::create($validator->validated());

            return redirect()->back()->with('success', 'FAQ created successfully.');
        } catch (Exception $e) {
            throw $e;
            return handleErrors($e);
        }
    }

    public function updateFAQ(Request $request, $id)
    {
        try {
            $faq = FAQ::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'question' => 'nullable|string|max:255',
                'answer' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return handleErrors(
                    new Exception($validator->errors()->first()),
                    'Validation failed',
                    422
                );
            }

            $faq->fill($validator->validated());
            $faq->save();

            return redirect()->back()->with('success', 'FAQ updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteFAQ($id)
    {
        try {
            $faq = FAQ::findOrFail($id);
            $faq->delete();

            return redirect()->back()->with('success', 'FAQ deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteSelectedFAQ(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No faq selected for deletion.');
            }

            $faqs = FAQ::whereIn('id', $ids)->get();

            foreach ($faqs as $q) {
                $q->delete();
            }

            return redirect()->back()->with('success', 'Selected faqs deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected faqs.");
        }
    }


    public function deleteAllFAQ()
    {
        try {
            $faqs = FAQ::all();

            foreach ($faqs as $q) {
                $q->delete();
            }

            return redirect()->back()->with('success', 'All faqs deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all faqs.");
        }
    }
}
