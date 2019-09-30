<?php

namespace API\UserBundle\EventListener;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @final
 */
class PasswordSettingListener implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::SET_PASSWORD_SUCCESSFUL => 'onSetPasswordSuccessful',
            UserEvent::SET_PASSWORD_COMPLETED => 'onSettingPassword',
        ];
    }

    public function onSetPasswordSuccessful(UserEvent $event): void
    {
        $user = $event->getUser();

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
    }

    public function onSettingPassword(UserEvent $event): void
    {
        $this->mailer->sendPasswordSettingEmailMessage($event->getUser());
    }
}
