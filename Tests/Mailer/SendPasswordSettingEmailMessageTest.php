<?php

namespace API\UserBundle\Tests\Mailer;

use API\UserBundle\Mailer\Mailer;

class SendPasswordSettingEmailMessageTest extends BaseMailerTestCase
{
    private $mailer;

    public function setUp()
    {
        parent::setUp();

        $this->parameters['password.setting.template'] = 'foo';

        $this->mailer = new Mailer(
            $this->swiftMailer,
            $this->renderer,
            $this->parameters
        );
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testSendPasswordSettingEmailMessageWithValidEmails(string $emailAddress): void
    {
        $this->setUserData($emailAddress);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->user,
                $this->parameters['password.setting.template']
            )
            ->willReturn('foo_content')
        ;

        $this->mailer->sendPasswordSettingEmailMessage($this->user);

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
    public function testSendPasswordResettingEmailMessageWithInvalidEmails(string $emailAddress): void
    {
        $this->setUserData($emailAddress);
        $this->mailer->sendPasswordSettingEmailMessage($this->user);
    }
}
