<?php

namespace API\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Flex\Recipe;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class CheckForMailerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('api_user.mailer')) {
            return;
        }

        if ($container->has('mailer')) {
            return;
        }

        if ($container->findDefinition('api_user.mailer')->hasTag('api_user.requires_swift')) {
            $message = 'A feature you activated in UserBundle requires the "mailer" service to be available.';

            if (class_exists(Recipe::class)) {
                $message .= ' Run "composer require swiftmailer-bundle" to install SwiftMailer or configure a different 
                              mailer in "config/packages/api_user.yaml".';
            }

            throw new \LogicException($message);
        }
    }
}
