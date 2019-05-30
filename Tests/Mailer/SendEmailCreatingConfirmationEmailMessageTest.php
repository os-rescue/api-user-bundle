<?php

namespace API\UserBundle\Tests\Mailer;

use API\UserBundle\Mailer\Mailer;

class SendEmailCreatingConfirmationEmailMessageTest extends BaseMailerTestCase
{
    private $mailer;

    public function setUp()
    {
        parent::setUp();

        $this->parameters['email.creating.template'] = 'foo';

        $this->mailer = new Mailer(
            $this->swiftMailer,
            $this->eventDispatcher,
            $this->renderer,
            $this->parameters
        );
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testSendEmailCreatingConfirmationEmailMessageWithValidEmail(string $emailAddress): void
    {
        $this->setUserData($emailAddress);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->user,
                $this->parameters['email.creating.template'],
                'api_user_confirm_email'
            )
            ->willReturn('foo_content')
        ;

        $this->mailer->sendEmailCreatingConfirmationEmailMessage($this->user);

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
    public function testSendEmailCreatingConfirmationEmailMessageWithInvalidEmails(string $emailAddress): void
    {
        $this->setUserData($emailAddress);
        $this->mailer->sendEmailCreatingConfirmationEmailMessage($this->user);
    }
}
