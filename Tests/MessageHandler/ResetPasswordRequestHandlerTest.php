<?php

namespace API\UserBundle\Tests\MessageHandler;

use API\UserBundle\Entity\ResetPasswordRequest;
use API\UserBundle\Mailer\MailerInterface;
use API\UserBundle\MessageHandler\ResetPasswordRequestHandler;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResetPasswordRequestHandlerTest extends KernelTestCase
{
    private $mailer;
    private $tokenGenerator;
    private $resetPasswordRequest;
    private $retryTtl;
    private $user;
    private $userManager;

    protected function setUp()
    {
        parent::setUp();

        self::bootKernel();

        $this->mailer = $this->getMockBuilder(MailerInterface::class)->getMock();
        $this->tokenGenerator = $this->getMockBuilder(TokenGeneratorInterface::class)->getMock();
        $this->retryTtl = self::$container->getParameter('api_user.password.resetting.retry_ttl');
        $this->user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->userManager = $this->getMockBuilder(UserManagerInterface::class)->getMock();

        $this->resetPasswordRequest = new ResetPasswordRequest();
        $this->resetPasswordRequest->email = 'foo';
    }

    public function testResetPasswordRequestHandlerWithNotFoundUser(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn(null)
        ;
        $this->userManager
            ->expects($this->never())
            ->method('refreshUser')
        ;
        $this->user
            ->expects($this->never())
            ->method('isAccountNonLocked')
        ;
        $this->user
            ->expects($this->never())
            ->method('isPasswordRequestNonExpired')
        ;
        $this->user
            ->expects($this->never())
            ->method('getConfirmationToken')
        ;
        $this->tokenGenerator
            ->expects($this->never())
            ->method('generateToken')
        ;
        $this->user
            ->expects($this->never())
            ->method('setConfirmationToken')
        ;
        $this->user
            ->expects($this->never())
            ->method('setPasswordRequestedAt')
        ;
        $this->userManager
            ->expects($this->never())
            ->method('updateUser')
        ;
        $this->mailer
            ->expects($this->never())
            ->method('sendPasswordResettingEmailMessage')
        ;

        $resetPasswordRequestHandler = $this->getInstance();
        $resetPasswordRequestHandler($this->resetPasswordRequest);
    }

    public function testResetPasswordRequestHandlerWithLockedAccount(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn($this->user)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('refreshUser')
            ->with($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(false)
        ;
        $this->user
            ->expects($this->never())
            ->method('isPasswordRequestNonExpired')
        ;
        $this->user
            ->expects($this->never())
            ->method('getConfirmationToken')
        ;
        $this->tokenGenerator
            ->expects($this->never())
            ->method('generateToken')
        ;
        $this->user
            ->expects($this->never())
            ->method('setConfirmationToken')
        ;
        $this->user
            ->expects($this->never())
            ->method('setPasswordRequestedAt')
        ;
        $this->userManager
            ->expects($this->never())
            ->method('updateUser')
        ;
        $this->mailer
            ->expects($this->never())
            ->method('sendPasswordResettingEmailMessage')
        ;

        $resetPasswordRequestHandler = $this->getInstance();
        $resetPasswordRequestHandler($this->resetPasswordRequest);
    }

    public function testResetPasswordRequestHandlerWithNonExpiredRequest(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn($this->user)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('refreshUser')
            ->with($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(true)
        ;
        $this->user
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->retryTtl)
            ->willReturn(true)
        ;
        $this->user
            ->expects($this->never())
            ->method('getConfirmationToken')
        ;
        $this->tokenGenerator
            ->expects($this->never())
            ->method('generateToken')
        ;
        $this->user
            ->expects($this->never())
            ->method('setConfirmationToken')
        ;
        $this->user
            ->expects($this->never())
            ->method('setPasswordRequestedAt')
        ;
        $this->userManager
            ->expects($this->never())
            ->method('updateUser')
        ;
        $this->mailer
            ->expects($this->never())
            ->method('sendPasswordResettingEmailMessage')
        ;

        $resetPasswordRequestHandler = $this->getInstance();
        $resetPasswordRequestHandler($this->resetPasswordRequest);
    }

    public function testResetPasswordRequestHandlerWithoutConfirmationToken(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn($this->user)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('refreshUser')
            ->with($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(true)
        ;
        $this->user
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->retryTtl)
            ->willReturn(false)
        ;
        $this->user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn(null)
        ;
        $this->tokenGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->willReturn('bar')
        ;
        $this->user
            ->expects($this->once())
            ->method('setConfirmationToken')
            ->with('bar')
        ;
        $this->user
            ->expects($this->once())
            ->method('setPasswordRequestedAt')
            ->with($this->isInstanceOf(\DateTime::class))
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($this->user)
        ;
        $this->mailer
            ->expects($this->once())
            ->method('sendPasswordResettingEmailMessage')
            ->with($this->user)
        ;

        $resetPasswordRequestHandler = $this->getInstance();
        $resetPasswordRequestHandler($this->resetPasswordRequest);
    }

    public function testResetPasswordRequestHandlerWithConfirmationToken(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn($this->user)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('refreshUser')
            ->with($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(true)
        ;
        $this->user
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->retryTtl)
            ->willReturn(false)
        ;
        $this->user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn('bar')
        ;
        $this->tokenGenerator
            ->expects($this->never())
            ->method('generateToken')
        ;
        $this->user
            ->expects($this->never())
            ->method('setConfirmationToken')
        ;
        $this->user
            ->expects($this->once())
            ->method('setPasswordRequestedAt')
            ->with($this->isInstanceOf(\DateTime::class))
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($this->user)
        ;
        $this->mailer
            ->expects($this->once())
            ->method('sendPasswordResettingEmailMessage')
            ->with($this->user)
        ;

        $resetPasswordRequestHandler = $this->getInstance();
        $resetPasswordRequestHandler($this->resetPasswordRequest);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testResetPasswordRequestHandlerThrowsException(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn($this->user)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('refreshUser')
            ->with($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(true)
        ;
        $this->user
            ->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->retryTtl)
            ->willReturn(false)
        ;
        $this->user
            ->expects($this->once())
            ->method('getConfirmationToken')
            ->willReturn('bar')
        ;
        $this->tokenGenerator
            ->expects($this->never())
            ->method('generateToken')
        ;
        $this->user
            ->expects($this->never())
            ->method('setConfirmationToken')
        ;
        $this->user
            ->expects($this->once())
            ->method('setPasswordRequestedAt')
            ->with($this->isInstanceOf(\DateTime::class))
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($this->user)
            ->willThrowException(new \PDOException())
        ;
        $this->mailer
            ->expects($this->never())
            ->method('sendPasswordResettingEmailMessage')
        ;

        $resetPasswordRequestHandler = $this->getInstance();
        $resetPasswordRequestHandler($this->resetPasswordRequest);
    }

    private function getInstance(): ResetPasswordRequestHandler
    {
        return new ResetPasswordRequestHandler(
            $this->mailer,
            $this->userManager,
            $this->tokenGenerator,
            $this->retryTtl
        );
    }
}
