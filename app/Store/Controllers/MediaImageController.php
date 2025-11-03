<?php

namespace App\Store\Controllers;

use App\Store\Models\MediaImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;

class MediaImageController
{
    public function viewAdminMediaImageListPage()
    {
        try {
            $mediaImages = MediaImage::orderBy('priority', 'asc')
                ->paginate(10);

            $mediaImages->getCollection()->transform(function ($image) {
                return $image->jsonResponse();
            });

            return view('pages.admin.dashboard.media_image.media_image_list', [
                'mediaImages' => $mediaImages,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createMediaImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'type' => 'required|string|max:100',
                'image' => 'required|image|max:4096',
                'link' => 'nullable|url|max:255',
                'priority' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'start_at' => 'nullable|date',
                'end_at' => 'nullable|date|after_or_equal:start_at',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            $validated = $validator->validated();

            $imagePath = Storage::disk('public')->putFile('store/media_images', $request->file('image'));

            MediaImage::create([
                'title' => $validated['title'] ?? null,
                'type' => $validated['type'],
                'image_path' => $imagePath,
                'link' => $validated['link'] ?? null,
                'priority' => $validated['priority'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
                'start_at' => $validated['start_at'] ?? null,
                'end_at' => $validated['end_at'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Media image created successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateMediaImage(Request $request, $id)
    {
        try {
            $media = MediaImage::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:100',
                'image' => 'nullable|image|max:4096',
                'link' => 'nullable|url|max:255',
                'priority' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'start_at' => 'nullable|date',
                'end_at' => 'nullable|date|after_or_equal:start_at',
                'remove_image' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
            }

            $validated = $validator->validated();

            if ($request->boolean('remove_image') && $media->image_path) {
                Storage::disk('public')->delete($media->image_path);
                $media->image_path = null;
            }

            if ($request->hasFile('image')) {
                if ($media->image_path && Storage::disk('public')->exists($media->image_path)) {
                    Storage::disk('public')->delete($media->image_path);
                }
                $media->image_path = Storage::disk('public')->putFile('store/media_images', $request->file('image'));
            }

            $media->fill([
                'title' => $validated['title'] ?? $media->title,
                'type' => $validated['type'] ?? $media->type,
                'link' => $validated['link'] ?? $media->link,
                'priority' => $validated['priority'] ?? $media->priority,
                'is_active' => $validated['is_active'] ?? $media->is_active,
                'start_at' => $validated['start_at'] ?? $media->start_at,
                'end_at' => $validated['end_at'] ?? $media->end_at,
            ]);

            $media->save();

            return redirect()->back()->with('success', 'Media image updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteMediaImage($id)
    {
        try {
            $media = MediaImage::findOrFail($id);

            if ($media->image_path) {
                Storage::disk('public')->delete($media->image_path);
            }

            $media->delete();

            return redirect()->back()->with('success', 'Media image deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
