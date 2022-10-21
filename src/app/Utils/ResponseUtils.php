<?php

namespace App\Utils;

class ResponseUtils
{
    public static function sendResponseWithSuccess($message, $data, $code)
    {
        return  response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function sendResponseWithError($message, $code, $error = '')
    {
        return  response()->json([
            'success' => false,
            'message' => $message,
            'error' => [
                'message' => $message,
            ],
        ], $code);
    }

    public static function sendResponseWithErrorAndData($message, $data, $code)
    {
        return  response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function sendResponseWithoutData($message, $code)
    {
        return  response()->json([
            'success' => true,
            'message' => $message,
        ], $code);
    }
}
