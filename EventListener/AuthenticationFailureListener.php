<?php

namespace API\UserBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AuthenticationFailureListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailureResponse',
        ];
    }

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $message = $event->getException()->getMessage();
        if (empty($message)) {
            return;
        }

        /** @var JWTAuthenticationFailureResponse $response */
        $response = $event->getResponse();
        $response->setMessage($message);
        $event->setResponse($response);
    }
}
