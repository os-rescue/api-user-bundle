<?php

namespace API\UserBundle\DependencyInjection;

use API\UserBundle\DependencyInjection\APIUserExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class APIUserExtensionTest extends TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    protected function tearDown(): void
    {
        $this->configuration = null;
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessUserModelClassSet(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['user_class']);
        $loader->load([$config], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessLoginCredentialIsValid(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getEmptyConfig();
        $config['login_credential'] = 'foo';
        $loader->load([$config], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessFromEmailSet(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['from_email']);
        $loader->load([$config], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessFromEmailAddressSet(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['from_email']['address']);
        $loader->load([$config], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessFromEmailSenderNameSet(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['from_email']['sender_name']);
        $loader->load([$config], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessEmailFromEmailAddressSet(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getFullConfig();
        unset($config['email']['from_email']['address']);
        $loader->load([$config], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessEmailSenderNameSet(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getFullConfig();
        unset($config['email']['from_email']['sender_name']);
        $loader->load([$config], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessPasswordFromEmailAddressSet(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getFullConfig();
        unset($config['password']['from_email']['address']);
        $loader->load([$config], new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessPasswordSenderNameSet(): void
    {
        $loader = new APIUserExtension();
        $config = $this->getFullConfig();
        unset($config['password']['from_email']['sender_name']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testUserLoadModelClassWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Acme\Document\User', 'api_user.model.user.class');
    }

    public function testUserLoadModelClass(): void
    {
        $this->createFullConfiguration();

        $this->assertParameter('Acme\Entity\User', 'api_user.model.user.class');
    }

    public function testUserLoadLoginCredentialWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('username', 'api_user.login_credential');
    }

    public function testUserLoadLoginCredential(): void
    {
        $this->createFullConfiguration();

        $this->assertParameter('email', 'api_user.login_credential');
    }

    public function testUserLoadEmailWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(['admin@acme.org' => 'Acme Corp'], 'api_user.email.from_email');
        $this->assertParameter('@APIUser/Email/creating.txt.twig', 'api_user.email.creating.template');
        $this->assertParameter('@APIUser/Email/updating.txt.twig', 'api_user.email.updating.template');
    }

    public function testUserLoadPasswordWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(['admin@acme.org' => 'Acme Corp'], 'api_user.password.from_email');
        $this->assertParameter('@APIUser/Password/changing.txt.twig', 'api_user.password.changing.template');
        $this->assertParameter('@APIUser/Password/setting.txt.twig', 'api_user.password.setting.template');
        $this->assertParameter(7200, 'api_user.password.resetting.retry_ttl');
        $this->assertParameter(86400, 'api_user.password.resetting.token_ttl');
        $this->assertParameter(
            '@APIUser/Password/resetting.txt.twig',
            'api_user.password.resetting.template'
        );
    }

    public function testUserLoadEmail(): void
    {
        $this->createFullConfiguration();

        $this->assertParameter(
            ['email@acme.org' => 'Acme Email'],
            'api_user.email.from_email'
        );
        $this->assertParameter(
            'AcmeMyBundle:Email:creating.txt.twig',
            'api_user.email.creating.template'
        );
        $this->assertParameter(
            'AcmeMyBundle:Email:updating.txt.twig',
            'api_user.email.updating.template'
        );
    }

    public function testUserLoadPassword(): void
    {
        $this->createFullConfiguration();

        $this->assertParameter(
            ['password@acme.org' => 'Acme Pass'],
            'api_user.password.from_email'
        );
        $this->assertParameter(
            'AcmeMyBundle:Password:mail.txt.twig',
            'api_user.password.changing.template'
        );
        $this->assertParameter(
            'AcmeMyBundle:Password:setting.txt.twig',
            'api_user.password.setting.template'
        );
        $this->assertParameter(
            10800,
            'api_user.password.resetting.retry_ttl'
        );
        $this->assertParameter(
            172800,
            'api_user.password.resetting.token_ttl'
        );
        $this->assertParameter(
            'AcmeMyBundle:Password:resetting.txt.twig',
            'api_user.password.resetting.template'
        );
    }

    public function testUserLoadUtilServiceWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertAlias(
            'API\UserBundle\Doctrine\UserManager',
            'API\UserBundle\Model\UserManagerInterface'
        );
        $this->assertAlias('API\UserBundle\Mailer\Mailer', 'API\UserBundle\Mailer\MailerInterface');
        $this->assertAlias(
            'API\UserBundle\Mailer\EmailTemplateRenderer',
            'API\UserBundle\Mailer\EmailTemplateRendererInterface'
        );
        $this->assertAlias(
            'API\UserBundle\Mailer\EmailTemplateUrlGenerator',
            'API\UserBundle\Mailer\EmailTemplateUrlGeneratorInterface'
        );
        $this->assertAlias('API\UserBundle\Util\Canonicalizer', 'api_user.util.email_canonicalizer');
        $this->assertAlias('API\UserBundle\Util\Canonicalizer', 'api_user.util.username_canonicalizer');
        $this->assertAlias(
            'API\UserBundle\Util\TokenGenerator',
            'API\UserBundle\Util\TokenGeneratorInterface'
        );
    }

    public function testUserLoadEventListenerService(): void
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('API\UserBundle\EventListener\AuthenticationListener');
        $this->assertHasDefinition('API\UserBundle\EventListener\EmailConfirmationListener');
        $this->assertHasDefinition('API\UserBundle\EventListener\PasswordChangingListener');
        $this->assertHasDefinition('API\UserBundle\EventListener\PasswordSettingListener');
        $this->assertHasDefinition('API\UserBundle\EventListener\PasswordResettingListener');
        $this->assertHasDefinition('API\UserBundle\EventListener\ResetPasswordRequestSubscriber');
        $this->assertHasDefinition('API\UserBundle\EventListener\TokenListener');
        $this->assertHasDefinition('api_user.listener.exception');
    }

    public function testUserLoadUtilService(): void
    {
        $this->createFullConfiguration();

        $this->assertAlias('acme_my.user_manager', 'API\UserBundle\Model\UserManagerInterface');
        $this->assertAlias('acme_my.mailer', 'API\UserBundle\Mailer\MailerInterface');
        $this->assertAlias('acme_my.renderer', 'API\UserBundle\Mailer\EmailTemplateRendererInterface');
        $this->assertAlias(
            'acme_my.url_generator',
            'API\UserBundle\Mailer\EmailTemplateUrlGeneratorInterface'
        );
        $this->assertAlias('acme_my.email_canonicalizer', 'api_user.util.email_canonicalizer');
        $this->assertAlias('acme_my.username_canonicalizer', 'api_user.util.username_canonicalizer');
        $this->assertAlias('acme_my.token_generator', 'API\UserBundle\Util\TokenGeneratorInterface');
    }

    public function testUserLoadDoctrineService(): void
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('API\UserBundle\Doctrine\UserListener');
        $this->assertHasDefinition('api_user.object_manager');
    }

    protected function createEmptyConfiguration(): void
    {
        $this->configuration = new ContainerBuilder();
        $loader = new APIUserExtension();
        $config = $this->getEmptyConfig();
        $loader->load([$config], $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    protected function createFullConfiguration(): void
    {
        $this->configuration = new ContainerBuilder();
        $loader = new APIUserExtension();
        $config = $this->getFullConfig();
        $loader->load([$config], $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    protected function getEmptyConfig(): array
    {
        $yaml = <<<EOF
user_class: Acme\Document\User
from_email:
    address: admin@acme.org
    sender_name: Acme Corp
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
user_class: Acme\Entity\User
login_credential: email
from_email:
    address: custom@acme.org
    sender_name: Acme Custom
email:
    from_email:
        address: email@acme.org
        sender_name: Acme Email
    creating:
        template: AcmeMyBundle:Email:creating.txt.twig
    updating:
        template: AcmeMyBundle:Email:updating.txt.twig
password:
    from_email:
        address: password@acme.org
        sender_name: Acme Pass
    changing:
        template: AcmeMyBundle:Password:mail.txt.twig
    setting:
        template: AcmeMyBundle:Password:setting.txt.twig
    resetting:
        retry_ttl: 10800
        token_ttl: 172800
        template: AcmeMyBundle:Password:resetting.txt.twig
        
service:
    mailer: acme_my.mailer
    renderer: acme_my.renderer
    url_generator: acme_my.url_generator
    email_canonicalizer: acme_my.email_canonicalizer
    username_canonicalizer: acme_my.username_canonicalizer
    token_generator: acme_my.token_generator
    user_manager: acme_my.user_manager
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function assertParameter($value, string $key): void
    {
        $this->assertSame($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    private function assertAlias(string $value, string $key): void
    {
        $this->assertSame($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    private function assertHasDefinition($id): void
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }
}
