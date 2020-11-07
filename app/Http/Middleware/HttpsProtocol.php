<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 10.03.2017
 * Time: 16:34
 */

namespace App\Http\Middleware;

use Closure;

class HttpsProtocol
{
    public function handle($request, Closure $next)
    {
        if (!$request->secure() && config('app.env') === 'production' && config('app.ssl')) {
            $request->setTrustedProxies([$request->getClientIp()]);

            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
