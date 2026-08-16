<?php

namespace App\Monitoring;

use Sentry\Event;
use Sentry\EventHint;
use Sentry\UserDataBag;

final class SentryEventScrubber
{
    private const SENSITIVE_KEY_PATTERN = '/authorization|cookie|password|token|secret|api[_-]?key|email|phone|body|data/i';

    /**
     * Keep error events useful without sending credentials, request bodies, or user PII.
     */
    public static function handle(Event $event, ?EventHint $hint = null): ?Event
    {
        $request = $event->getRequest();

        if ($request !== []) {
            unset($request['data'], $request['cookies'], $request['query_string']);

            $event->setRequest(self::scrub($request));
        }

        $event->setExtra(self::scrub($event->getExtra()));

        foreach ($event->getContexts() as $name => $context) {
            $event->setContext($name, self::scrub($context));
        }

        // The Laravel integration can discover an email address and IP address from the
        // authenticated user. Keep only the internal ID for production diagnostics.
        $user = $event->getUser();
        $event->setUser(
            $user?->getId() !== null
                ? UserDataBag::createFromUserIdentifier($user->getId())
                : null
        );

        return $event;
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    private static function scrub(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $safe = [];

        foreach ($value as $key => $item) {
            $key = (string) $key;

            if (preg_match(self::SENSITIVE_KEY_PATTERN, $key) === 1) {
                $safe[$key] = '[Filtered]';
                continue;
            }

            if (in_array(strtolower($key), ['url', 'referer'], true) && is_string($item)) {
                $safe[$key] = self::withoutQueryString($item);
                continue;
            }

            $safe[$key] = self::scrub($item);
        }

        return $safe;
    }

    private static function withoutQueryString(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false) {
            return explode('?', explode('#', $url, 2)[0], 2)[0];
        }

        $path = $parts['path'] ?? '/';

        if (! isset($parts['scheme'], $parts['host'])) {
            return $path;
        }

        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return $parts['scheme'].'://'.$parts['host'].$port.$path;
    }
}
