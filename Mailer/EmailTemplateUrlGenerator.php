<?php

namespace API\UserBundle\Mailer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @final
 */
class EmailTemplateUrlGenerator implements EmailTemplateUrlGeneratorInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function generateRoute(?string $route, ?string $token, ?string ...$args): ?string
    {
        if (null === $route) {
            return null;
        }

        return $this->router->generate(
            $route,
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
