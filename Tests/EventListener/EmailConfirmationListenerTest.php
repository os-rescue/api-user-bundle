<?php

namespace API\UserBundle\Tests\EventListener;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\EventListener\EmailConfirmationListener;
use API\UserBundle\Mailer\MailerInterface;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Util\TokenGenerator;
use API\UserBundle\Util\TokenGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EmailConfirmationListenerTest extends TestCase
{
    private $event;
    private $userManager;
    private $mailer;
    private $tokenGenerator;

    protected function setUp()
    {
        $this->userManager = $this->getMockBuilder(UserManagerInterface::class)->getMock();
        $this->mailer = $this->getMockBuilder(MailerInterface::class)->getMock();
        $this->tokenGenerator = $this->getMockBuilder(TokenGeneratorInterface::class)->getMock();
        $this->event = $this->getMockBuilder(UserEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testOnCreatingEmailWithSettingConfirmationToken(): void
    {
        $user = $this->getUser();
        $token = (new TokenGenerator())->generateToken();

        $this->event
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn(null)
        ;
        $this->tokenGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->willReturn($token)
        ;
        $user
            ->expects($this->once())
            ->method('setConfirmationToken')
            ->with($token)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($user)
        ;
        $this->mailer
            ->expects($this->once())
            ->method('sendEmailCreatingConfirmationEmailMessage')
            ->with($user)
        ;

        $listener = new EmailConfirmationListener(
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator
        );
        $listener->onCreatingEmail($this->event);
    }

    public function testOnCreatingEmailWithoutSettingConfirmationToken(): void
    {
        $user = $this->getUser();
        $token = (new TokenGenerator())->generateToken();

        $this->event
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn($token)
        ;
        $this->tokenGenerator
            ->expects($this->never())
            ->method('generateToken')
        ;
        $user
            ->expects($this->never())
            ->method('setConfirmationToken')
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($user)
        ;
        $this->mailer
            ->expects($this->once())
            ->method('sendEmailCreatingConfirmationEmailMessage')
            ->with($user)
        ;

        $listener = new EmailConfirmationListener(
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator
        );
        $listener->onCreatingEmail($this->event);
    }

    public function testOnUpdatingEmailWithSettingConfirmationToken(): void
    {
        $user = $this->getUser();
        $token = (new TokenGenerator())->generateToken();

        $this->event
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn(null)
        ;
        $this->tokenGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->willReturn($token)
        ;
        $user
            ->expects($this->once())
            ->method('setConfirmationToken')
            ->with($token)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($user)
        ;
        $this->mailer
            ->expects($this->once())
            ->method('sendEmailUpdatingConfirmationEmailMessage')
            ->with($user)
        ;

        $listener = new EmailConfirmationListener(
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator
        );
        $listener->onUpdatingEmail($this->event);
    }

    public function testOnUpdatingEmailWithoutSettingConfirmationToken(): void
    {
        $user = $this->getUser();
        $token = (new TokenGenerator())->generateToken();

        $this->event
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn($token)
        ;
        $this->tokenGenerator
            ->expects($this->never())
            ->method('generateToken')
        ;
        $user
            ->expects($this->never())
            ->method('setConfirmationToken')
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($user)
        ;
        $this->mailer
            ->expects($this->once())
            ->method('sendEmailUpdatingConfirmationEmailMessage')
            ->with($user)
        ;

        $listener = new EmailConfirmationListener(
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator
        );
        $listener->onUpdatingEmail($this->event);
    }

    protected function getUser(): MockObject
    {
        return $this->getMockBuilder(UserInterface::class)->getMock();
    }
}
