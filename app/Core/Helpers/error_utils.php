<?php

if (!function_exists('handleErrors')) {
    function handleErrors(?Exception $error = null, $message = null, int $status = 500)
    {
        if (request()->expectsJson()) {
            $response = [
                'success' => false,
                'message' => $message,
            ];
            if ($error) {
                $response['error'] = $error->getMessage();
            }
            return response()->json($response, $status);
        }
        return redirect()->back()->withInput()->with('error', $message ?? $error->getMessage() ?? "Something Went Wrong");
    }
}
