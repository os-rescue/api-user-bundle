<?php

namespace API\UserBundle\Mailer;

interface EmailTemplateUrlGeneratorInterface
{
    public function generateRoute(?string $route, ?string $token, ?string ...$args): ?string;
}
