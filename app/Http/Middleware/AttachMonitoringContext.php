<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AttachMonitoringContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $this->requestId($request);
        $request->attributes->set('monitoring.request_id', $requestId);

        $this->applyContext($request, $requestId);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $this->applyContext($request, $requestId, 500);

            throw $exception;
        }

        $this->applyContext($request, $requestId, $response->getStatusCode());
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }

    private function applyContext(Request $request, string $requestId, ?int $status = null): void
    {
        $route = $request->route();
        $routeName = is_object($route) && method_exists($route, 'getName')
            ? $route->getName()
            : null;
        $action = is_object($route) && method_exists($route, 'getActionName')
            ? $route->getActionName()
            : null;
        $userId = $request->user()?->getAuthIdentifier();
        $tenantId = $this->tenantId($request, $route);
        $routeModel = $this->routeModel($route);

        $requestContext = [
            'id' => $requestId,
            'method' => $request->method(),
            'path' => '/'.$request->path(),
            'route' => $routeName,
            'action' => $action,
            'status' => $status,
        ];

        $clientContext = [
            'platform' => $this->clientPlatform($request),
            'app_version' => $this->header($request, 'X-Aqari-App-Version'),
            'app_build' => $this->header($request, 'X-Aqari-App-Build'),
            'origin' => $this->header($request, 'Origin'),
        ];

        Log::withContext([
            'request_id' => $requestId,
            'route' => $routeName,
            'path' => $requestContext['path'],
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'client_platform' => $clientContext['platform'],
        ]);

        if (! function_exists('Sentry\\configureScope')) {
            return;
        }

        \Sentry\configureScope(static function (Scope $scope) use ($requestContext, $clientContext, $routeModel, $userId, $tenantId): void {
            $scope->setTag('client.platform', $clientContext['platform']);
            $scope->setTag('http.method', $requestContext['method']);
            $scope->setTag('http.route', $requestContext['route'] ?? 'unmatched');

            if ($requestContext['status'] !== null) {
                $scope->setTag('http.status_code', (string) $requestContext['status']);
            }

            $scope->setContext('request', $requestContext);
            $scope->setContext('client', $clientContext);
            $scope->setContext('route_model', $routeModel);
            $scope->setContext('tenant', ['id' => $tenantId]);
            $scope->setUser($userId === null ? [] : ['id' => (string) $userId]);
        });
    }

    private function requestId(Request $request): string
    {
        $candidate = $request->header('X-Request-ID');

        if (is_string($candidate) && preg_match('/^[A-Za-z0-9-]{16,64}$/', $candidate) === 1) {
            return $candidate;
        }

        return (string) Str::uuid();
    }

    private function clientPlatform(Request $request): string
    {
        $platform = strtolower((string) $this->header($request, 'X-Aqari-Platform'));

        return in_array($platform, ['nativephp-ios', 'nativephp-android'], true)
            ? $platform
            : 'web';
    }

    private function tenantId(Request $request, mixed $route): int|string|null
    {
        $tenant = $request->attributes->get('mobile_tenant');

        if ($tenant instanceof Model) {
            return $tenant->getKey();
        }

        foreach ($this->routeParameters($route) as $parameter) {
            if ($parameter instanceof Model && class_basename($parameter) === 'Tenant') {
                return $parameter->getKey();
            }
        }

        return null;
    }

    /**
     * @return array{parameter: string|null, class: string|null, key: int|string|null, identifier: string|null}
     */
    private function routeModel(mixed $route): array
    {
        foreach ($this->routeParameters($route) as $name => $parameter) {
            if (! $parameter instanceof Model) {
                continue;
            }

            $identifier = null;
            foreach (['code', 'slug', 'uuid'] as $attribute) {
                $value = $parameter->getAttribute($attribute);

                if (is_scalar($value) && $value !== '') {
                    $identifier = (string) $value;
                    break;
                }
            }

            return [
                'parameter' => (string) $name,
                'class' => class_basename($parameter),
                'key' => $parameter->getKey(),
                'identifier' => $identifier,
            ];
        }

        return [
            'parameter' => null,
            'class' => null,
            'key' => null,
            'identifier' => null,
        ];
    }

    private function header(Request $request, string $name): ?string
    {
        $value = $request->header($name);

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : Str::limit($value, 120, '');
    }

    /**
     * @return array<string, mixed>
     */
    private function routeParameters(mixed $route): array
    {
        if (! is_object($route) || ! method_exists($route, 'parameters')) {
            return [];
        }

        try {
            return $route->parameters();
        } catch (Throwable) {
            return [];
        }
    }
}
