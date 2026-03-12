<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('404 - Page Not Found') }} - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1e3a8a 0%, #581c87 100%);
            }
        }
        .container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            margin: 1rem 0 0.5rem;
        }
        .error-message {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0 0 2rem;
            line-height: 1.6;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
        }
        .btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }
        .btn svg {
            width: 1rem;
            height: 1rem;
        }
        @supports (font-family: 'Noto Sans Arabic') {
            html[dir="rtl"] body {
                font-family: 'Noto Sans Arabic', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">{{ __('Page Not Found') }}</h2>
        <p class="error-message">
            {{ __("The page you're looking for doesn't exist or has been moved. Please check the URL or return to the previous page.") }}
        </p>
        <a href="{{ url()->previous() ?: '/' }}" class="btn">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Go Back') }}
        </a>
    </div>
</body>
</html>
