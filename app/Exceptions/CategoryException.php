<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryException extends Exception
{
    public function __construct(string $message = 'Category operation failed', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Category Error',
                'message' => $this->getMessage()
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['error' => $this->getMessage()])
            ->withInput();
    }

    public function report(): void
    {
        \Illuminate\Support\Facades\Log::error('Category Exception: ' . $this->getMessage(), [
            'exception' => $this,
            'trace' => $this->getTraceAsString()
        ]);
    }
}