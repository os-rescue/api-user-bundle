<?php

namespace API\UserBundle\Tests\MessageHandler;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Message\UserEmailUpdate;
use API\UserBundle\MessageHandler\AsyncEmailUpdatingHandler;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Tests\Common\EventDispatcherTestCase;

class AsyncEmailUpdatingHandlerTest extends EventDispatcherTestCase
{
    private $user;
    private $userManager;

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->userManager = $this->getMockBuilder(UserManagerInterface::class)->getMock();
    }

    public function testAsyncEmailUpdatingHandlerWithEnabledUserAndNonLockedAccount(): void
    {
        $uuid = '123';

        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['id' => $uuid])
            ->willReturn($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true)
        ;
        $this->user
            ->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(true)
        ;

        $asyncEmailUpdatingHandler = new AsyncEmailUpdatingHandler($this->eventDispatcher, $this->userManager);
        $asyncEmailUpdatingHandler(new UserEmailUpdate($uuid));

        $this->assertDispatchedUserEvent(UserEvent::EMAIL_UPDATED, 1);
    }

    public function testAsyncEmailUpdatingHandlerWithNoneEnabledUser(): void
    {
        $uuid = '123';

        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['id' => $uuid])
            ->willReturn($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false)
        ;
        $this->user
            ->expects($this->never())
            ->method('isAccountNonLocked')
        ;

        $asyncEmailUpdatingHandler = new AsyncEmailUpdatingHandler($this->eventDispatcher, $this->userManager);
        $asyncEmailUpdatingHandler(new UserEmailUpdate($uuid));

        $this->assertDispatchedUserEvent(UserEvent::EMAIL_UPDATED, 0);
    }

    public function testAsyncEmailUpdatingHandlerWithEnabledUserAndLockedAccount(): void
    {
        $uuid = '123';

        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['id' => $uuid])
            ->willReturn($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true)
        ;
        $this->user
            ->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(false)
        ;

        $asyncEmailUpdatingHandler = new AsyncEmailUpdatingHandler($this->eventDispatcher, $this->userManager);
        $asyncEmailUpdatingHandler(new UserEmailUpdate($uuid));

        $this->assertDispatchedUserEvent(UserEvent::EMAIL_UPDATED, 0);
    }

    public function testAsyncEmailUpdatingHandlerWithNotFoundUser(): void
    {
        $uuid = '123';

        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->with(['id' => $uuid])
            ->willReturn(null)
        ;

        $asyncEmailUpdatingHandler = new AsyncEmailUpdatingHandler($this->eventDispatcher, $this->userManager);
        $asyncEmailUpdatingHandler(new UserEmailUpdate($uuid));

        $this->assertDispatchedUserEvent(UserEvent::EMAIL_UPDATED, 0);
    }
}
