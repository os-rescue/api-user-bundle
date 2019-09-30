<?php

namespace API\UserBundle\Tests\Controller;

use API\UserBundle\Controller\PromoteUser;
use API\UserBundle\Event\UserEvent;
use API\UserBundle\Tests\Common\EventDispatcherTestCase;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;

class PromoteUserTest extends EventDispatcherTestCase
{
    private $user;
    private $userManager;

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->userManager = $this->getMockBuilder(UserManagerInterface::class)->getMock();
    }

    public function testPromoteUserSuccess(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->willReturn($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('setSuperAdmin')
            ->with(true)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($this->user)
        ;

        $demoteUserController = $this->getInstance();
        $demoteUserController($this->user);

        $this->assertDispatchedUserEvent(UserEvent::USER_PROMOTED, 1);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage User not found.
     */
    public function testDemoteUserFailedWithNotFoundUser(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->willReturn(null)
        ;
        $this->user
            ->expects($this->never())
            ->method('setSuperAdmin')
        ;
        $this->userManager
            ->expects($this->never())
            ->method('updateUser')
        ;

        $demoteUserController = $this->getInstance();
        $demoteUserController($this->user);

        $this->assertDispatchedUserEvent(UserEvent::USER_PROMOTED, 0);
    }

    /**
     * @expectedException \ApiPlatform\Core\Exception\RuntimeException
     * @expectedExceptionMessage User promotion failed.
     */
    public function testDemoteUserFailedWithPdoException(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserBy')
            ->willReturn($this->user)
        ;
        $this->user
            ->expects($this->once())
            ->method('setSuperAdmin')
            ->with(true)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->willThrowException(new \PDOException())
        ;

        $demoteUserController = $this->getInstance();
        $demoteUserController($this->user);

        $this->assertDispatchedUserEvent(UserEvent::USER_PROMOTED, 0);
    }

    private function getInstance(): PromoteUser
    {
        return new PromoteUser(
            $this->eventDispatcher,
            $this->userManager
        );
    }
}
