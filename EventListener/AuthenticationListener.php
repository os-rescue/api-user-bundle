<?php

namespace API\UserBundle\EventListener;

use API\UserBundle\Event\FilterUserResponseEvent;
use API\UserBundle\Event\UserEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

/**
 * @final
 */
class AuthenticationListener implements EventSubscriberInterface
{
    private $authenticationSuccessHandler;
    private $userChecker;

    public function __construct(
        AuthenticationSuccessHandler $authenticationSuccessHandler,
        UserCheckerInterface $userChecker
    ) {
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->userChecker = $userChecker;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::EMAIL_CONFIRMED => 'authenticate',
            UserEvent::RESET_PASSWORD_COMPLETED => 'authenticate',
        ];
    }

    public function authenticate(FilterUserResponseEvent $event): void
    {
        $user = $event->getUser();

        $this->userChecker->checkPreAuth($user);

        $response = $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);

        $event->setResponse($response);
    }
}
