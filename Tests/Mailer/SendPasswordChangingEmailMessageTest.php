<?php

namespace API\UserBundle\Tests\Mailer;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Mailer\Mailer;

class SendPasswordChangingEmailMessageTest extends BaseMailerTestCase
{
    private $mailer;

    public function setUp()
    {
        parent::setUp();

        $this->parameters['password.changing.template'] = 'bar';

        $this->mailer = new Mailer(
            $this->swiftMailer,
            $this->renderer,
            $this->parameters
        );
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testSendPasswordChangingEmailMessageWithValidEmails(string $emailAddress): void
    {
        $this->setUserData($emailAddress);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->user,
                $this->parameters['password.changing.template']
            )
            ->willReturn('foo_content')
        ;

        $this->mailer->sendPasswordChangingEmailMessage($this->user);

        $this->assertSame(1, $this->testEmailListener->getSendEmailCount());

        $message = $this->testEmailListener->getMessage(0);
        $this->assertNotNull($message);

        $this->assertSame([
            'bar@example.com' => 'BAR Ltd'
        ], $message->getFrom());

        $this->assertSameReceiver($message, $emailAddress);
    }

    /**
     * @dataProvider invalidEmailProvider
     * @expectedException \Swift_RfcComplianceException
     */
    public function testSendPasswordChangingEmailMessageWithInvalidEmails(string $emailAddress): void
    {
        $this->setUserData($emailAddress);
        $this->mailer->sendPasswordChangingEmailMessage($this->user);
    }
}
