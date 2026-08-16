import * as Sentry from '@sentry/browser';

const configuredDsn = import.meta.env.VITE_SENTRY_DSN;
const configuredPlatform = import.meta.env.VITE_SENTRY_PLATFORM;
const configuredRelease = import.meta.env.VITE_SENTRY_RELEASE;
const configuredEnvironment = import.meta.env.VITE_SENTRY_ENVIRONMENT;

const appBuild = window.__AQARI_BUILD || {};
const platform = resolvePlatform(configuredPlatform);
const enabled = typeof configuredDsn === 'string' && configuredDsn.trim() !== '';

if (enabled) {
    Sentry.init({
        dsn: configuredDsn,
        environment: configuredEnvironment || 'production',
        release: configuredRelease || appBuild.release || undefined,
        sendDefaultPii: false,
        beforeBreadcrumb(breadcrumb) {
            if (breadcrumb.category === 'console') {
                return null;
            }

            if (breadcrumb.category === 'fetch' || breadcrumb.category === 'xhr') {
                if (breadcrumb.data?.url) {
                    breadcrumb.data.url = safeUrl(breadcrumb.data.url);
                }

                delete breadcrumb.data?.body;
                delete breadcrumb.data?.headers;
            }

            return breadcrumb;
        },
        beforeSend(event) {
            if (event.request) {
                event.request.url = safeUrl(event.request.url);
                delete event.request.data;
                delete event.request.cookies;
                delete event.request.headers;
            }

            if (event.user) {
                event.user = event.user.id ? { id: String(event.user.id) } : undefined;
            }

            return event;
        },
    });

    Sentry.setTags({
        'client.platform': platform,
        'app.version': String(appBuild.version || 'unknown'),
        'app.build': String(appBuild.build || 'unknown'),
    });

    Sentry.setContext('nativephp', {
        runtime: window.location.protocol === 'php:' ? 'nativephp-mobile' : 'browser',
        platform,
        version: appBuild.version || null,
        build: appBuild.build || null,
    });
}

function captureApiFailure({ error, method, requestId, status, type, url }) {
    if (! enabled) {
        return;
    }

    const exception = error instanceof Error
        ? error
        : new Error(status ? `API request failed with HTTP ${status}` : 'API request failed before a response was received');

    exception.name = 'MobileApiRequestError';

    Sentry.withScope((scope) => {
        scope.setTag('error.source', 'mobile-api');
        scope.setTag('api.failure_type', type || 'unknown');
        scope.setTag('api.method', method || 'GET');

        if (status) {
            scope.setTag('api.status_code', String(status));
        }

        scope.setContext('api_request', {
            request_id: requestId || null,
            method: method || 'GET',
            status: status || null,
            type: type || 'unknown',
            url: safeUrl(url),
        });

        Sentry.captureException(exception);
    });
}

function resolvePlatform(value) {
    if (value === 'nativephp-ios' || value === 'nativephp-android') {
        return value;
    }

    if (window.location.protocol !== 'php:') {
        return 'web';
    }

    return /Android/i.test(navigator.userAgent)
        ? 'nativephp-android'
        : 'nativephp-ios';
}

function safeUrl(value) {
    if (! value) {
        return null;
    }

    try {
        const url = new URL(String(value), window.location.origin);

        return `${url.origin}${url.pathname}`;
    } catch {
        return String(value).split(/[?#]/, 1)[0];
    }
}

window.AqariMonitoring = {
    captureApiFailure,
    enabled,
    platform,
};
