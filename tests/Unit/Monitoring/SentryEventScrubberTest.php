<?php

namespace Tests\Unit\Monitoring;

use App\Monitoring\SentryEventScrubber;
use PHPUnit\Framework\TestCase;
use Sentry\Event;
use Sentry\UserDataBag;

class SentryEventScrubberTest extends TestCase
{
    public function test_it_removes_sensitive_request_and_user_data(): void
    {
        $event = Event::createEvent();
        $event->setRequest([
            'url' => 'https://aqarismart.com/api/mobile/auth/login?email=user@example.com',
            'headers' => [
                'authorization' => 'Bearer secret-token',
                'x-request-id' => 'request-123',
            ],
            'data' => ['password' => 'do-not-send'],
            'cookies' => ['session' => 'do-not-send'],
        ]);
        $event->setExtra([
            'token' => 'do-not-send',
            'request_id' => 'request-123',
        ]);
        $event->setUser(UserDataBag::createFromArray([
            'id' => 36,
            'email' => 'user@example.com',
            'ip_address' => '127.0.0.1',
        ]));

        SentryEventScrubber::handle($event);

        self::assertSame([
            'url' => 'https://aqarismart.com/api/mobile/auth/login',
            'headers' => [
                'authorization' => '[Filtered]',
                'x-request-id' => 'request-123',
            ],
        ], $event->getRequest());
        self::assertSame([
            'token' => '[Filtered]',
            'request_id' => 'request-123',
        ], $event->getExtra());
        self::assertSame(36, $event->getUser()?->getId());
        self::assertNull($event->getUser()?->getEmail());
        self::assertNull($event->getUser()?->getIpAddress());
    }
}
