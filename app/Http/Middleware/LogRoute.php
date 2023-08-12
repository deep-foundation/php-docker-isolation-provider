<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogRoute
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $data = [
            'URI' => $request->getUri(),
            'METHOD' => $request->getMethod(),
            'HEADERS' => collect($request->header())->transform(function ($item) {
                return $item[0];
            })->toArray(),
            'RESPONSE' => $response->getContent(),
        ];

        $hasError = false;
        if (!empty($response->exception)) {
            $hasError = true;
        }

        if ($hasError) {
            Log::channel('deep')->error(json_encode($data));
        } else {
            Log::channel('deep')->info(json_encode($data));
        }

        return $response;
    }
}
