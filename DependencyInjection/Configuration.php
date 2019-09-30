<?php

namespace API\UserBundle\DependencyInjection;

use API\UserBundle\Doctrine\UserManager;
use API\UserBundle\Mailer\EmailTemplateRenderer;
use API\UserBundle\Mailer\EmailTemplateUrlGenerator;
use API\UserBundle\Mailer\Mailer;
use API\UserBundle\Util\Canonicalizer;
use API\UserBundle\Util\TokenGenerator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('api_user');

        $rootNode
            ->children()
                ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
                ->enumNode('login_credential')
                    ->values(['username', 'email'])
                    ->defaultValue('username')
                ->end()
                ->arrayNode('from_email')
                    ->isRequired()
                    ->children()
                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->append($this->addEmailNode())
                ->append($this->addPasswordNode())
                ->append($this->addEmailNode())
                ->append($this->addRoleNode())
                ->append($this->addServiceNode())
            ->end();

        return $treeBuilder;
    }

    private function addEmailNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('email');

        $node = $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->arrayNode('from_email')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('creating')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('@APIUser/Email/creating.txt.twig')->end()
                    ->end()
                ->end()
                ->arrayNode('updating')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('@APIUser/Email/updating.txt.twig')->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    private function addPasswordNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('password');

        $node = $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->arrayNode('from_email')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('changing')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('@APIUser/Password/changing.txt.twig')->end()
                    ->end()
                ->end()
                ->arrayNode('setting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('@APIUser/Password/setting.txt.twig')->end()
                    ->end()
                ->end()
                ->append($this->addPasswordResettingNode())
            ->end();

        return $node;
    }

    private function addPasswordResettingNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('resetting');

        $node = $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('retry_ttl')->defaultValue(7200)->end()
                ->scalarNode('token_ttl')->defaultValue(86400)->end()
                ->scalarNode('template')->defaultValue('@APIUser/Password/resetting.txt.twig')->end()
            ->end();

        return $node;
    }

    private function addServiceNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('service');

        $node = $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('user_manager')->defaultValue(UserManager::class)->end()
                ->scalarNode('mailer')->defaultValue(Mailer::class)->end()
                ->scalarNode('renderer')->defaultValue(EmailTemplateRenderer::class)->end()
                ->scalarNode('url_generator')->defaultValue(EmailTemplateUrlGenerator::class)->end()
                ->scalarNode('email_canonicalizer')->defaultValue(Canonicalizer::class)->end()
                ->scalarNode('username_canonicalizer')->defaultValue(Canonicalizer::class)->end()
                ->scalarNode('token_generator')->defaultValue(TokenGenerator::class)->end()
            ->end();

        return $node;
    }

    private function addRoleNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('role');

        $node = $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->arrayNode('from_email')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('promoting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('@APIUser/Role/promoting.txt.twig')->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
