<?php

namespace API\UserBundle\Tests\Mailer;

use API\UserBundle\Mailer\Mailer;

class SendRolePromotingEmailMessageTest extends BaseMailerTestCase
{
    private $mailer;

    public function setUp()
    {
        parent::setUp();

        $this->parameters['role.promoting.template'] = 'foo';

        $this->mailer = new Mailer(
            $this->swiftMailer,
            $this->renderer,
            $this->parameters
        );
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testSendRolePromotingEmailMessageWithValidEmail(string $emailAddress): void
    {
        $this->setUserData($emailAddress);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->user,
                $this->parameters['role.promoting.template']
            )
            ->willReturn('foo_content')
        ;

        $this->mailer->sendRolePromotingEmailMessage($this->user);

        $this->assertSame(1, $this->testEmailListener->getSendEmailCount());

        $message = $this->testEmailListener->getMessage(0);
        $this->assertNotNull($message);

        $this->assertSame([
            'foo@example.com' => 'FOO Ltd'
        ], $message->getFrom());

        $this->assertSameReceiver($message, $emailAddress);
    }

    /**
     * @dataProvider invalidEmailProvider
     * @expectedException \Swift_RfcComplianceException
     */
    public function testSendRolePromotingEmailMessageWithInvalidEmails(string $emailAddress): void
    {
        $this->setUserData($emailAddress);
        $this->mailer->sendRolePromotingEmailMessage($this->user);
    }
}
