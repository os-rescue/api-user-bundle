<?php

namespace API\UserBundle\Controller;

use API\UserBundle\Event\FilterUserResponseEvent;
use API\UserBundle\Event\GetUserByTokenEvent;
use API\UserBundle\Event\UserEvent;
use API\UserBundle\Model\UserManagerInterface;
use ApiPlatform\Core\Exception\RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ConfirmEmail
{
    private $userManager;
    private $eventDispatcher;

    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route(
     *     name="api_user_confirm_email",
     *     path="/api/confirm/{token}",
     *     methods={"GET"},
     * )
     */
    public function __invoke(string $token)
    {
        $tokenEvent = new GetUserByTokenEvent($token);
        $this->eventDispatcher->dispatch(
            UserEvent::EMAIL_CONFIRMATION_INITIALIZE,
            $tokenEvent
        );

        $user = $tokenEvent->getUser();
        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        try {
            $this->userManager->updateUser($user);

            $event = new FilterUserResponseEvent($user);
            $this->eventDispatcher->dispatch(
                FilterUserResponseEvent::EMAIL_CONFIRMED,
                $event
            );

            if (null === $response = $event->getResponse()) {
                throw new RuntimeException('The authentication process is failed.');
            }

            return $response;
        } catch (\PDOException $e) {
            throw new RuntimeException('Activation of the account failed.', $e->getCode(), $e);
        }
    }
}
