<?php

namespace API\UserBundle\EventListener;

use API\UserBundle\Model\UserManagerInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\LockedException;

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
            throw new NotFoundHttpException('user.not_found.');
        }

        if (!$user->isAccountNonLocked()) {
            throw new LockedException('user.account_locked.');
        }

        if ($user->isPasswordRequestNonExpired($this->retryTtl)) {
            throw new BadRequestHttpException('user.check_email.');
        }
    }
}
