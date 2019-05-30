<?php

namespace API\UserBundle\Tests\Common;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TestEventDispatcher extends EventDispatcher
{
    /**
     * @var [event_name => Event[]]
     */
    private static $events = [];

    /**
     * @param string     $eventName
     * @param Event|null $event
     *
     * @return Event
     */
    public function dispatch($eventName, Event $event = null): Event
    {
        if (!isset(self::$events[$eventName])) {
            self::$events[$eventName] = [];
        }
        self::$events[$eventName][] = $event;

        return parent::dispatch($eventName, $event);
    }

    public function getDispatchedEvents(): array
    {
        return self::$events;
    }

    public function getDispatchedEventsByName(string $eventName): array
    {
        if (isset(self::$events[$eventName])) {
            return self::$events[$eventName];
        }

        return [];
    }

    /**
     * reset memorized events.
     */
    public static function resetEvents(): void
    {
        self::$events = [];
    }
}
