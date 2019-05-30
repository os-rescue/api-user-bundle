<?php

namespace API\UserBundle\Tests\Common;

class TestEmailListener implements \Swift_Events_SendListener
{
    /**
     * @var \Swift_Events_SendEvent[]
     */
    private static $sendEvents = [];

    public function beforeSendPerformed(\Swift_Events_SendEvent $evt): void
    {
    }

    public function sendPerformed(\Swift_Events_SendEvent $evt): void
    {
        self::$sendEvents[$evt->getMessage()->getId()] = $evt;
    }

    public static function reset(): void
    {
        self::$sendEvents = [];
    }

    public function getSendEmailCount(): int
    {
        return count(self::$sendEvents);
    }

    public function getMessage(int $index = 0): ?\Swift_Message
    {
        return !empty(array_values(self::$sendEvents)[$index]) ?
            array_values(self::$sendEvents)[$index]->getMessage() :
            null;
    }
}
