<?php

namespace API\UserBundle\MessageHandler;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Message\UserEmailUpdate;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AsyncEmailUpdatingHandler implements MessageHandlerInterface
{
    private $eventDispatcher;
    private $userManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, UserManagerInterface $userManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
    }

    public function __invoke(UserEmailUpdate $message): void
    {
        $user = $this->userManager->findUserBy(['id' => $message->getUuid()]);

        if (!$user || !$this->canBeNotified($user)) {
            return;
        }

        $this->eventDispatcher->dispatch(UserEvent::EMAIL_UPDATED, new UserEvent($user));
    }

    private function canBeNotified(UserInterface $user): bool
    {
        return $user->isEnabled() && $user->isAccountNonLocked();
    }
}
