<?php

namespace API\UserBundle\Mailer;

use API\UserBundle\Model\UserInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @final
 */
class EmailTemplateRenderer implements EmailTemplateRendererInterface
{
    private $templating;
    private $emailTemplateUrlGenerator;

    public function __construct(
        EngineInterface $templating,
        EmailTemplateUrlGeneratorInterface $emailTemplateUrlGenerator
    ) {
        $this->templating = $templating;
        $this->emailTemplateUrlGenerator = $emailTemplateUrlGenerator;
    }

    public function render(
        UserInterface $user,
        string $template,
        string $route = null,
        array $templateParams = []
    ): string {
        $url = $this->emailTemplateUrlGenerator->generateRoute($route, $user->getConfirmationToken());

        $templateParams = array_merge(
            $templateParams,
            null !== $url ? ['user' => $user, 'confirmationUrl' => $url] : ['user' => $user]
        );

        return $this->templating->render($template, $templateParams);
    }
}
