<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends Exception
{
    public function render($request, Throwable $exception): JsonResponse
    {
        if (($exception instanceof ModelNotFoundException) || ($exception instanceof NotFoundHttpException)) {
            return response()->json([
                'message' => 'Registro não encontrado'
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
