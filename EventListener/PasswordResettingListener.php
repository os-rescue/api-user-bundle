<?php

namespace API\UserBundle\EventListener;

use API\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PasswordResettingListener implements EventSubscriberInterface
{
    private $tokenTtl;

    public function __construct(int $tokenTtl)
    {
        $this->tokenTtl = $tokenTtl;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::RESET_PASSWORD_STARTED => 'onResetPasswordStarted',
            UserEvent::RESET_PASSWORD_SUCCESSFUL => 'onResetPasswordSuccessful',
        ];
    }

    public function onResetPasswordStarted(UserEvent $event): void
    {
        $user = $event->getUser();

        if (!$user->isPasswordRequestNonExpired($this->tokenTtl)) {
            throw new BadRequestHttpException('Token expired.');
        }
    }

    public function onResetPasswordSuccessful(UserEvent $event): void
    {
        $user = $event->getUser();

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
    }
}
