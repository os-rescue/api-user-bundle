<?php

namespace API\UserBundle\MessageHandler;

use API\UserBundle\Entity\ResetPasswordRequest;
use API\UserBundle\Mailer\MailerInterface;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResetPasswordRequestHandler implements MessageHandlerInterface
{
    private $mailer;
    private $retryTtl;
    private $tokenGenerator;
    private $userManager;

    public function __construct(
        MailerInterface $mailer,
        UserManagerInterface $userManager,
        TokenGeneratorInterface $tokenGenerator,
        int $retryTtl
    ) {
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->retryTtl = $retryTtl;
    }

    public function __invoke(ResetPasswordRequest $resetPasswordRequest): void
    {
        try {
            $user = $this->userManager->findUserBy(['email' => $resetPasswordRequest->email]);
            if (!$user || $user->isPasswordRequestNonExpired($this->retryTtl)) {
                return;
            }

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendPasswordResettingEmailMessage($user);

            $user->setPasswordRequestedAt(new \DateTime());
            $this->userManager->updateUser($user);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Reset password request failed.', $exception->getCode(), $exception);
        }
    }
}
