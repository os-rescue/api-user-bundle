<?php

namespace API\UserBundle\Mailer;

use API\UserBundle\Model\UserInterface;

interface EmailTemplateRendererInterface
{
    public function render(
        UserInterface $user,
        string $template,
        string $route = null,
        array $templateParams = []
    ): string;
}
