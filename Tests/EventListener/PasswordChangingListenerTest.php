<?php

namespace API\UserBundle\Tests\EventListener;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\EventListener\PasswordChangingListener;
use API\UserBundle\Mailer\MailerInterface;
use API\UserBundle\Model\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PasswordChangingListenerTest extends TestCase
{
    private $event;
    private $mailer;

    protected function setUp()
    {
        $this->mailer = $this->getMockBuilder(MailerInterface::class)->getMock();
        $this->event = $this->getMockBuilder(UserEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testUserRegistrationSuccess(): void
    {
        $user = $this->getUser();

        $this->event->expects($this->once())->method('getUser')->willReturn($user);
        $this->mailer->expects($this->once())->method('sendPasswordChangingEmailMessage')->with($user);

        $listener = new PasswordChangingListener($this->mailer);
        $listener->onChangingPassword($this->event);
    }

    protected function getUser(): MockObject
    {
        return $this->getMockForAbstractClass(User::class);
    }
}
