<?php

namespace App\Commands;

use Symfony\Component\HttpFoundation\Response;

class ResponseJsonCommand
{
    public static function responseSuccess(int $code = Response::HTTP_OK, $data, string $message = "success")
    {
        $res = [
            "status" => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($res,$code);
    }

    public static function responseFail(int $code = Response::HTTP_BAD_REQUEST, string $message, $data = null)
    {
        $res = [
            'status' => 'fail',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($res,$code);
    }

    public static function responseError(int $code = Response::HTTP_INTERNAL_SERVER_ERROR, string $message, $data = null)
    {
        $res = [
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($res,$code);
    }
}
