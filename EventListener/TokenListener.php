<?php

namespace API\UserBundle\EventListener;

use API\UserBundle\Event\GetUserByTokenEvent;
use API\UserBundle\Event\UserEvent;
use API\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @final
 */
class TokenListener implements EventSubscriberInterface
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::SET_PASSWORD_INITIALIZE => 'findUserByToken',
            UserEvent::RESET_PASSWORD_INITIALIZE => 'findUserByToken',
            UserEvent::EMAIL_CONFIRMATION_INITIALIZE => 'findUserByToken',
        ];
    }

    public function findUserByToken(GetUserByTokenEvent $event): void
    {
        $user = $this->userManager->findUserByConfirmationToken($event->getToken());

        if (null === $user) {
            throw new NotFoundHttpException('Token invalid.');
        }

        $event->setUser($user);
    }
}
