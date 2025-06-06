<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandException extends Exception
{
    public function __construct(string $message = 'Brand operation failed', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Brand Error',
                'message' => $this->getMessage()
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['error' => $this->getMessage()])
            ->withInput();
    }

    public function report(): void
    {
        \Illuminate\Support\Facades\Log::error('Brand Exception: ' . $this->getMessage(), [
            'exception' => $this,
            'trace' => $this->getTraceAsString()
        ]);
    }
}