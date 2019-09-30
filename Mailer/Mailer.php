<?php

namespace API\UserBundle\Mailer;

use API\UserBundle\Model\UserInterface;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 *
 * @final
 */
final class Mailer implements MailerInterface
{
    public const ROUTE_PATH_CONFIRM_EMAIL = 'api_user_confirm_email';
    public const ROUTE_PATH_RESET_PASSWORD = 'api_user_reset_password';

    private $mailer;
    private $renderer;
    private $parameters;

    public function __construct(
        \Swift_Mailer $mailer,
        EmailTemplateRendererInterface $renderer,
        array $parameters
    ) {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->parameters = $parameters;
    }

    public function sendEmailCreatingConfirmationEmailMessage(UserInterface $user): int
    {
        $template = $this->renderer->render(
            $user,
            $this->parameters['email.creating.template'],
            self::ROUTE_PATH_CONFIRM_EMAIL
        );

        return $this->sendEmailMessage($user, $template, $this->parameters['from_email']['email']);
    }

    public function sendEmailUpdatingConfirmationEmailMessage(UserInterface $user): int
    {
        $template = $this->renderer->render(
            $user,
            $this->parameters['email.updating.template'],
            self::ROUTE_PATH_CONFIRM_EMAIL
        );

        return $this->sendEmailMessage($user, $template, $this->parameters['from_email']['email']);
    }

    public function sendPasswordChangingEmailMessage(UserInterface $user): int
    {
        $template = $this->renderer->render(
            $user,
            $this->parameters['password.changing.template']
        );

        return $this->sendEmailMessage($user, $template, $this->parameters['from_email']['password']);
    }

    public function sendPasswordSettingEmailMessage(UserInterface $user): int
    {
        $template = $this->renderer->render(
            $user,
            $this->parameters['password.setting.template']
        );

        return $this->sendEmailMessage($user, $template, $this->parameters['from_email']['password']);
    }

    public function sendPasswordResettingEmailMessage(UserInterface $user): int
    {
        $template = $this->renderer->render(
            $user,
            $this->parameters['password.resetting.template'],
            self::ROUTE_PATH_RESET_PASSWORD
        );

        return $this->sendEmailMessage($user, $template, $this->parameters['from_email']['password']);
    }

    public function sendRolePromotingEmailMessage(UserInterface $user): int
    {
        $template = $this->renderer->render(
            $user,
            $this->parameters['role.promoting.template']
        );

        return $this->sendEmailMessage($user, $template, $this->parameters['from_email']['email']);
    }

    private function sendEmailMessage(UserInterface $user, string $renderedTemplate, $fromEmail): int
    {
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = \array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($user->getEmail(), (string) $user)
            ->setBody($body, 'text/html');

        return $this->mailer->send($message);
    }
}
