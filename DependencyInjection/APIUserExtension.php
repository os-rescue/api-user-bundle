<?php

namespace API\UserBundle\DependencyInjection;

use API\UserBundle\Mailer\EmailTemplateRendererInterface;
use API\UserBundle\Mailer\EmailTemplateUrlGeneratorInterface;
use API\UserBundle\Mailer\MailerInterface;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class APIUserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter('api_user.model.user.class', $config['user_class']);

        $basenames = [
            'controller',
            'doctrine',
            'form_type',
            'listeners',
            'mailer',
            'util',
            'validator',
            'security',
            'message_handlers'
        ];
        foreach ($basenames as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $container->setParameter('api_user.login_credential', $config['login_credential']);
        $container->getDefinition('API\UserBundle\Validator\Initializer')
            ->setArgument(1, $config['login_credential'])
        ;
        $container->setAlias('api_user.util.email_canonicalizer', $config['service']['email_canonicalizer']);
        $container->setAlias('api_user.util.username_canonicalizer', $config['service']['username_canonicalizer']);

        $container->setAlias(
            MailerInterface::class,
            new Alias($config['service']['mailer'], false)
        );
        $container->setAlias(
            EmailTemplateRendererInterface::class,
            new Alias($config['service']['renderer'], false)
        );
        $container->setAlias(
            EmailTemplateUrlGeneratorInterface::class,
            new Alias($config['service']['url_generator'], false)
        );
        $container->setAlias(
            UserManagerInterface::class,
            new Alias($config['service']['user_manager'], false)
        );
        $container->setAlias(
            TokenGeneratorInterface::class,
            new Alias($config['service']['token_generator'], false)
        );

        $this->loadEmail($config['email'], $container, $config['from_email']);
        $this->loadPassword($config['password'], $container, $config['from_email']);
        $this->loadRole($config['role'], $container, $config['from_email']);
    }

    private function loadEmail(array $config, ContainerBuilder $container, array $fromEmail)
    {
        if (isset($config['from_email'])) {
            $fromEmail = $config['from_email'];
            unset($config['from_email']);
        }

        $container->setParameter(
            'api_user.email.from_email',
            [$fromEmail['address'] => $fromEmail['sender_name']]
        );
        $container->setParameter(
            'api_user.email.creating.template',
            $config['creating']['template']
        );
        $container->setParameter(
            'api_user.email.updating.template',
            $config['updating']['template']
        );
    }

    private function loadPassword(array $config, ContainerBuilder $container, array $fromEmail)
    {
        if (isset($config['from_email'])) {
            $fromEmail = $config['from_email'];
            unset($config['from_email']);
        }

        $container->setParameter(
            'api_user.password.from_email',
            [$fromEmail['address'] => $fromEmail['sender_name']]
        );
        $container->setParameter(
            'api_user.password.changing.template',
            $config['changing']['template']
        );
        $container->setParameter(
            'api_user.password.setting.template',
            $config['setting']['template']
        );
        $container->setParameter(
            'api_user.password.resetting.retry_ttl',
            $config['resetting']['retry_ttl']
        );
        $container->setParameter(
            'api_user.password.resetting.token_ttl',
            $config['resetting']['token_ttl']
        );
        $container->setParameter(
            'api_user.password.resetting.template',
            $config['resetting']['template']
        );
    }

    private function loadRole(array $config, ContainerBuilder $container, array $fromEmail)
    {
        if (isset($config['from_email'])) {
            $fromEmail = $config['from_email'];
            unset($config['from_email']);
        }

        $container->setParameter(
            'api_user.user.from_email',
            [$fromEmail['address'] => $fromEmail['sender_name']]
        );
        $container->setParameter(
            'api_user.role.promoting.template',
            $config['promoting']['template']
        );
    }
}
