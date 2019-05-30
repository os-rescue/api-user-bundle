<?php

namespace API\UserBundle\Tests\EventListener;

use API\UserBundle\Event\GetUserByTokenEvent;
use API\UserBundle\EventListener\TokenListener;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TokenListenerTest extends TestCase
{
    private $event;
    private $userManager;

    protected function setUp()
    {
        $this->event = $this->getMockBuilder(GetUserByTokenEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userManager = $this->getMockBuilder(UserManagerInterface::class)->getMock();
    }

    public function testFindUserByTokenSuccessful(): void
    {
        $user = $this->getUser();

        $this->userManager
            ->expects($this->once())
            ->method('findUserByConfirmationToken')
            ->willReturn($user)
        ;
        $this->event
            ->expects($this->once())
            ->method('setUser')
            ->with($user)
        ;

        $listener = new TokenListener($this->userManager);
        $listener->findUserByToken($this->event);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Token invalid.
     */
    public function testFindUserByTokenThrowsBadRequestHttpException(): void
    {
        $this->userManager
            ->expects($this->once())
            ->method('findUserByConfirmationToken')
            ->willReturn(null)
        ;
        $this->event
            ->expects($this->never())
            ->method('setUser')
        ;

        $listener = new TokenListener($this->userManager);
        $listener->findUserByToken($this->event);
    }

    private function getUser(): MockObject
    {
        return $this->getMockBuilder(UserInterface::class)->getMock();
    }
}
