<?php

namespace API\UserBundle\EventListener;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Mailer\MailerInterface;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @final
 */
class EmailConfirmationListener implements EventSubscriberInterface
{
    private $userManager;
    private $mailer;
    private $tokenGenerator;

    public function __construct(
        UserManagerInterface $userManager,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator
    ) {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::EMAIL_CREATED => 'onCreatingEmail',
            UserEvent::EMAIL_UPDATED => 'onUpdatingEmail',
        ];
    }

    public function onCreatingEmail(UserEvent $event): void
    {
        $user = $event->getUser();
        $this->updateUser($user);

        $this->mailer->sendEmailCreatingConfirmationEmailMessage($user);
    }

    public function onUpdatingEmail(UserEvent $event): void
    {
        $user = $event->getUser();
        $this->updateUser($user);

        $this->mailer->sendEmailUpdatingConfirmationEmailMessage($user);
    }

    private function updateUser(UserInterface $user): void
    {
        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        $this->userManager->updateUser($user);
    }
}
