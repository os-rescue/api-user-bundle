<?php

namespace API\UserBundle\Tests\Mailer;

use API\UserBundle\Mailer\Mailer;

class SendEmailUpdatingConfirmationEmailMessageTest extends BaseMailerTestCase
{
    private $mailer;

    public function setUp()
    {
        parent::setUp();

        $this->parameters['email.updating.template'] = 'foo';

        $this->mailer = new Mailer(
            $this->swiftMailer,
            $this->renderer,
            $this->parameters
        );
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testSendEmailUpdatingConfirmationEmailMessageWithValidEmails(string $emailAddress): void
    {
        $this->setUserData($emailAddress);
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->user,
                $this->parameters['email.updating.template'],
                'api_user_confirm_email'
            )
            ->willReturn('foo_content')
        ;

        $this->mailer->sendEmailUpdatingConfirmationEmailMessage($this->user);

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
    public function testSendEmailUpdatingConfirmationEmailMessageWithInvalidEmails(string $emailAddress): void
    {
        $this->setUserData($emailAddress);
        $this->mailer->sendEmailUpdatingConfirmationEmailMessage($this->user);
    }
}
