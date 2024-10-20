<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait AppResponse
{
    public function genRes($result, $status, $message = "", $errors = [])
    {
        return response()->json([
            "errors" => $errors,
            "message" => $message,
            "result" => $result,
        ], $status);
    }

    public function success($result, $message = "")
    {
        return $this->genRes($result, Response::HTTP_OK, $message); //HTTP_OK = 200
    }

    public function forbidden($message = "")
    {
        return $this->genRes(null, Response::HTTP_FORBIDDEN, $message); //HTTP_FORBIDDEN = 403 int
    }

    public function notFound($message = "")
    {
        return $this->genRes(null, Response::HTTP_NOT_FOUND, $message); //HTTP_NOT_FOUND = 404 int
    }

    public function unauthorized($message = "")
    {
        return $this->genRes(null, Response::HTTP_UNAUTHORIZED, $message); //HTTP_UNAUTHORIZED = 401 int
    }

    public function duplicateVlaue($message = ""): JsonResponse
    {
        return $this->genRes(null, Response::HTTP_CONFLICT, $message); // HTTP_CONFLICT = 409
    }

    public function unprocessableVlaue($message = ""): JsonResponse
    {
        return $this->genRes(null, Response::HTTP_UNPROCESSABLE_ENTITY, $message); // HTTP_CONFLICT = 422
    }
}
