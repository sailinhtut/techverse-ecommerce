<?php

namespace App\Core\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Pagination\LengthAwarePaginator;


class StorageManager
{
    protected $disk = 'public';

    public function show()
    {
        return view('pages.file_manager');
    }

    public function list(Request $request)
    {
        $validated = $request->validate([
            'path' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        try {
            $rawPath = $validated['path'] ?? '';
            $path = $this->sanitizePath($rawPath);

            $directories = Storage::disk($this->disk)->directories($path);
            $files = Storage::disk($this->disk)->files($path);

            $fileData = array_map(function ($file) {
                $disk = Storage::disk($this->disk);
                return [
                    'path' => $file,
                    'basename' => basename($file),
                    'size' => Storage::disk($this->disk)->size($file),
                    'human_size' => $this->humanFilesize(Storage::disk($this->disk)->size($file)),
                    'mime_type' => $disk->mimeType($file),
                    'modified' => date('Y-m-d h:i A', Storage::disk($this->disk)->lastModified($file)),
                    'download_url' => Storage::disk('public')->url($file),
                ];
            }, $files);

            $dirData = array_map(function ($dir) {
                return [
                    'path' => $dir,
                    'basename' => basename($dir),
                ];
            }, $directories);

            $page = $validated['page'] ?? 1;
            $perPage = $validated['per_page'] ?? 10;
            $offset = ($page - 1) * $perPage;

            $paginatedFiles = new LengthAwarePaginator(
                array_slice($fileData, $offset, $perPage, true),
                count($fileData),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            // Total size calculation
            $allFiles = Storage::disk($this->disk)->allFiles($path);
            $totalSize = array_sum(array_map(fn($f) => Storage::disk($this->disk)->size($f), $allFiles));

            return response()->json([
                'path' => $path,
                'directories' => $dirData,
                'files' => $paginatedFiles->items(),
                'current_page' => $paginatedFiles->currentPage(),
                'last_page' => $paginatedFiles->lastPage(),
                'per_page' => $paginatedFiles->perPage(),
                'total_files' => $paginatedFiles->total(),
                'total_size' => $totalSize,
                'total_human' => $this->humanFilesize($totalSize),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong',
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'path' => 'nullable|string',
            'file' => 'required|file|max:2048000',
            // 'file' => 'required|file|max:1024',
        ]);

        try {
            $path = $this->sanitizePath($validated['path'] ?? '');
            $file = $request->file('file');

            $originalName = $file->getClientOriginalName();
            $safeName = $this->sanitizeName(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();

            $finalName = $extension
                ? $safeName . '.' . $extension
                : $safeName;

            $targetDirectory = $path ?: '';
            $targetPath = $targetDirectory === ''
                ? $finalName
                : $targetDirectory . '/' . $finalName;

            if (Storage::disk($this->disk)->exists($targetPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File already exists in this directory',
                ], 400);
            }

            Storage::disk($this->disk)->putFileAs($targetDirectory, $file, $finalName);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'path' => $targetPath,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function createFolder(Request $request)
    {
        $validated = $request->validate([
            'path' => 'nullable|string',
            'name' => 'required|string|max:255',
        ]);

        try {
            $path = $this->sanitizePath($validated['path'] ?? '');
            $name = $this->sanitizeName($validated['name']);

            if (empty($name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid folder name',
                ], 400);
            }

            $newFolderPath = $path ? $path . '/' . $name : $name;

            // Prevent overwriting
            if (Storage::disk($this->disk)->exists($newFolderPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder already exists',
                ], 400);
            }

            Storage::disk($this->disk)->makeDirectory($newFolderPath);

            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully',
                'path' => $newFolderPath,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }



    public function rename(Request $request)
    {
        $validated = $request->validate([
            'old_path' => 'required|string',
            'new_name' => 'required|string',
        ]);

        try {
            $oldPath = $this->sanitizePath($validated['old_path']);
            $newName = $this->sanitizeName($validated['new_name']);

            if (!Storage::disk($this->disk)->exists($oldPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File or directory not found',
                ], 404);
            }

            $directory = dirname($oldPath);
            $basename = pathinfo($oldPath, PATHINFO_FILENAME); // old name without ext
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION); // ext only

            // keep extension if file has one
            $finalName = $extension
                ? $newName . '.' . $extension
                : $newName;

            $newPath = $directory === '.'
                ? $finalName
                : $directory . '/' . $finalName;

            // Check if new name already exists
            if (Storage::disk($this->disk)->exists($newPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'A file or directory with this name already exists',
                ], 400);
            }

            Storage::disk($this->disk)->move($oldPath, $newPath);

            return response()->json([
                'success' => true,
                'message' => 'Renamed successfully',
                'old_path' => $oldPath,
                'new_path' => $newPath,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something Went Wrong',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    public function delete(Request $request)
    {
        $validated = $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $path = $this->sanitizePath($validated['path']);

            if (!Storage::disk($this->disk)->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File or directory not found',
                ], 404);
            }

            if (
                Storage::disk($this->disk)->delete($path) ||
                Storage::disk($this->disk)->deleteDirectory($path)
            ) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deleted successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete resource',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    protected function sanitizePath($path)
    {
        $path = str_replace("\0", '', trim($path, '/'));
        if (Str::contains($path, '..')) {
            abort(400, 'Invalid path');
        }
        return $path;
    }

    protected function sanitizeName($name)
    {
        $name = str_replace(["\0", '/', '\\'], '', $name);
        return trim($name);
    }

    protected function humanFilesize($bytes, $decimals = 2)
    {
        $sz = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        if ($bytes == 0) return '0 B';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $sz[$factor];
    }
}
