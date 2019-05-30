<?php

namespace API\UserBundle\Tests\Common;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventDispatcherTestCase extends KernelTestCase
{
    protected $eventDispatcher;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();

        $this->eventDispatcher = self::$container->get('test.API\UserBundle\Tests\Common\TestEventDispatcher');
    }

    protected function assertDispatchedUserEvent(string $event, int $count = 1): void
    {
        $dispatchedEvents = $this->eventDispatcher->getDispatchedEventsByName($event);

        $this->assertNotNull($dispatchedEvents);
        $this->assertCount($count, $dispatchedEvents);

        if (0 !== $count) {
            /** @var UserEvent $event */
            $event = $dispatchedEvents[0];
            $this->assertNotNull($event);
            $this->assertInstanceOf(UserEvent::class, $event);
            $this->assertInstanceOf(UserInterface::class, $event->getUser());
        }
    }
}
