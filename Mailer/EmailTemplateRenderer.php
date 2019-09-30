<?php

namespace API\UserBundle\Mailer;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @final
 */
class EmailTemplateRenderer implements EmailTemplateRendererInterface
{
    private $eventDispatcher;
    private $templating;
    private $emailTemplateUrlGenerator;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EngineInterface $templating,
        EmailTemplateUrlGeneratorInterface $emailTemplateUrlGenerator
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->templating = $templating;
        $this->emailTemplateUrlGenerator = $emailTemplateUrlGenerator;
    }

    public function render(
        UserInterface $user,
        string $template,
        string $route = null,
        array $templateParams = []
    ): string {
        $this->eventDispatcher->dispatch(UserEvent::SENT_MAIL, new UserEvent($user));

        $url = $this->emailTemplateUrlGenerator->generateRoute($route, $user->getConfirmationToken());

        $templateParams = array_merge(
            $templateParams,
            null !== $url ? ['user' => $user, 'confirmationUrl' => $url] : ['user' => $user]
        );

        return $this->templating->render($template, $templateParams);
    }
}
