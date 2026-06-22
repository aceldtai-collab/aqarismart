<?php

namespace App\Http\Middleware;

use App\Services\Market\CurrentCountry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCountry
{
    public function __construct(protected CurrentCountry $countries)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $country = $this->countries->resolve($request);

        if ($request->hasSession()) {
            $request->session()->put(CurrentCountry::COOKIE, $country->iso2);
        }

        /** @var Response $response */
        $response = $next($request);

        if ($request->query->has('country')) {
            $response->headers->setCookie(cookie(
                CurrentCountry::COOKIE,
                $country->iso2,
                60 * 24 * 365,
                null,
                null,
                $request->isSecure(),
                false,
                false,
                'lax'
            ));
        }

        return $response;
    }
}
