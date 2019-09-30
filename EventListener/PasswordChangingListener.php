<?php

namespace API\UserBundle\EventListener;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @final
 */
class PasswordChangingListener implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::CHANGE_PASSWORD_COMPLETED => 'onChangingPassword',
            UserEvent::RESET_PASSWORD_COMPLETED => 'onChangingPassword',
        ];
    }

    public function onChangingPassword(UserEvent $event): void
    {
        $this->mailer->sendPasswordChangingEmailMessage($event->getUser());
    }
}
