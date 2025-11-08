<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class Handler extends ExceptionHandler
{
    // Daftar exception yang tidak dilaporkan
    protected $dontReport = [];

    // Daftar input yang tidak disimpan ke session saat validation error
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        //
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ThrottleRequestsException) {
            $retryAfter = $exception->getHeaders()['Retry-After'] ?? 60;

            return redirect()->back()
                ->with('retry_after', $retryAfter)
                ->withErrors(['error' => 'Terlalu banyak percobaan login, tunggu beberapa saat.']);
        }

        return parent::render($request, $exception);
    }
}
