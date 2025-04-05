<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\App;
use App\Services\LogService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PDOException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log all exceptions regardless of request type
            $this->saveLog($e, $this->getLogLevel($this->getExceptionStatusCode($e)), 
                'exception_handling', 
                $this->getExceptionStatusCode($e) >= 500 ? 'error' : 'fail', 
                $this->getExceptionStatusCode($e));
            
            // You can return false here if you want to prevent the default Laravel reporting
            // return false;
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return $this->handleApiException($e);
            }

            // If not an API request, let Laravel handle it as usual
            return null;
        });
    }

    private function handleApiException(Throwable $e)
    {
        // Special case for ValidationException since it has a different response format
        if ($e instanceof ValidationException) {
            return response()->json([
                'status' => 'fail',
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => "Validation failed",
                'errors' => $e->validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // For all other exceptions, use a standard format
        $code = $this->getExceptionStatusCode($e);
        $message = $e->getMessage() ?: $this->getDefaultMessageForCode($code);
        
        return $this->apiResponse($e, $code, $message);
    }

    private function apiResponse(Throwable $e, int $code, string $message)
    {
        return response()->json([
            'status' => $code >= 500 ? 'error' : 'fail',
            'code' => $code,
            'message' => $message,
            'data' => null
        ], $code);
    }

    private function getLogLevel(int $code): string
    {
        return match (true) {
            $code >= 500 => 'error',
            $code === Response::HTTP_FORBIDDEN => 'warning',
            $code === Response::HTTP_UNAUTHORIZED => 'info',
            default => 'debug',
        };
    }

    public function saveLog(Throwable $e, string $level = "error", string $task = 'api_exception_handling', $status = "error", $code = null)
    {
        if ($code === null) {
            $code = $e->getCode();
        }

        $req = request();
        $logService = App::make(LogService::class);
        $logService->level($level)
            ->status($status)
            ->code($code)
            ->task($task)
            ->message($e->getMessage())
            ->request($req)
            ->error($e)
            ->save();
    }

    private function getExceptionStatusCode(Throwable $e): int
    {
        return match (true) {
            $e instanceof AccessDeniedHttpException => Response::HTTP_FORBIDDEN,
            $e instanceof AuthenticationException => Response::HTTP_UNAUTHORIZED,
            $e instanceof AuthorizationException => Response::HTTP_FORBIDDEN,
            $e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException => Response::HTTP_NOT_FOUND,
            $e instanceof MethodNotAllowedHttpException => Response::HTTP_METHOD_NOT_ALLOWED,
            $e instanceof QueryException || $e instanceof PDOException => Response::HTTP_INTERNAL_SERVER_ERROR,
            $e instanceof ValidationException => Response::HTTP_UNPROCESSABLE_ENTITY,
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };
    }
    
    private function getDefaultMessageForCode(int $code): string
    {
        return match ($code) {
            Response::HTTP_UNAUTHORIZED => 'Unauthorized',
            Response::HTTP_FORBIDDEN => 'Forbidden',
            Response::HTTP_NOT_FOUND => 'Not Found',
            Response::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed', 
            Response::HTTP_UNPROCESSABLE_ENTITY => 'Validation Failed',
            Response::HTTP_INTERNAL_SERVER_ERROR => 'Server Error',
            default => 'An error occurred',
        };
    }
}