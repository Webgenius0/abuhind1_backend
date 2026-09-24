<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    public function success($message = null, $data = [], $code = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'code'    => $code
        ], $code);
    }

    public function error($message = null, $data = null, $code = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
            'code'    => $code
        ], $code);
    }
}
