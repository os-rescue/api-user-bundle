<?php

namespace API\UserBundle\Tests\Mailer;

use API\UserBundle\Mailer\EmailTemplateRenderer;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Tests\Common\EventDispatcherTestCase;
use API\UserBundle\Tests\Common\TestEmailListener;

class BaseMailerTestCase extends EventDispatcherTestCase
{
    protected $translator;
    protected $swiftMailer;
    protected $testEmailListener;
    protected $renderer;
    protected $user;
    protected $parameters = [
        'from_email' => [
            'email' => ['foo@example.com' => 'FOO Ltd'],
            'password' => ['bar@example.com' => 'BAR Ltd'],
        ]
    ];

    public function setUp()
    {
        parent::setUp();

        $this->swiftMailer = self::$container->get('mailer');
        $this->testEmailListener = self::$container->get(TestEmailListener::class);

        $this->user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->renderer = $this->getMockBuilder(EmailTemplateRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function validEmailProvider(): \Generator
    {
        yield ['foofoo@example.com'];
        yield ['barbar@example.com'];
        yield ['foobar@example.com'];
        yield ['barbar@example.com'];
    }

    public function invalidEmailProvider(): \Generator
    {
        yield ['foo'];
    }

    protected function setUserData(string $emailAddress): void
    {
        $this->user->method('getEmail')
            ->willReturn($emailAddress)
        ;
        $this->user->method('__toString')
            ->willReturn($emailAddress)
        ;
        $this->user->method('getUsername')
            ->willReturn($emailAddress)
        ;
    }

    protected function assertSameReceiver(\Swift_Mime_SimpleMessage $message, string $emailAddress): void
    {
        $this->assertSame([$emailAddress => $emailAddress], $message->getTo());
    }
}
