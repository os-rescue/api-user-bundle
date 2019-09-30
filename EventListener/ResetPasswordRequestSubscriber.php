<?php

namespace API\UserBundle\EventListener;

use API\UserBundle\Model\UserManagerInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ResetPasswordRequestSubscriber implements EventSubscriberInterface
{
    public const ROUTE_API_RESET_PASSWORD_REQUEST = 'api_reset_password_requests_post_collection';

    private $userManager;
    private $retryTtl;

    public function __construct(UserManagerInterface $userManager, int $retryTtl)
    {
        $this->userManager = $userManager;
        $this->retryTtl = $retryTtl;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onResetPasswordRequest', EventPriorities::PRE_RESPOND],
        ];
    }

    public function onResetPasswordRequest(GetResponseForControllerResultEvent $event): void
    {
        $request = $event->getRequest();

        if (self::ROUTE_API_RESET_PASSWORD_REQUEST !== $request->attributes->get('_route')) {
            return;
        }

        $content = \GuzzleHttp\json_decode($request->getContent());
        $user = $this->userManager->findUserBy(['email' => $content->email]);

        if (!$user) {
            throw new BadRequestHttpException();
        }

        if (!$user->isAccountNonLocked()) {
            throw new BadRequestHttpException();
        }

        if ($user->isPasswordRequestNonExpired($this->retryTtl)) {
            throw new BadRequestHttpException();
        }
    }
}
