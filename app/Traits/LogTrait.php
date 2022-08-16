<?php

namespace App\Traits;

use App\Models\Log;
use Exception;

trait LogTrait
{
    public function logger(string $severity, Exception $exception)
    {
        if (!env('APP_DEBUG')) {
            Log::create([
                'severity' => $severity,
                'message' => $exception->getMessage(),
                'stack' => str_replace(base_path(), '', $exception->getTraceAsString())
            ]);
        } else {
            dd($exception);
        }
    }
}
