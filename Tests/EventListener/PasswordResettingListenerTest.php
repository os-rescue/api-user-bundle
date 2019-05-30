<?php

namespace API\UserBundle\Tests\EventListener;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\EventListener\PasswordResettingListener;
use API\UserBundle\Model\UserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PasswordResettingListenerTest extends KernelTestCase
{
    private $event;

    protected function setUp()
    {
        self::bootKernel();

        $this->event = $this->getMockBuilder(UserEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testOnResetPasswordStarted(): void
    {
        $user = $this->getUser();

        $this->event->expects($this->once())->method('getUser')->willReturn($user);

        $user->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->getTokenTTl())
            ->willReturn(true)
        ;

        $listener = new PasswordResettingListener($this->getTokenTTl());
        $listener->onResetPasswordStarted($this->event);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Token expired.
     */
    public function testOnResetPasswordStartedThrowsBadRequestHttpException(): void
    {
        $user = $this->getUser();

        $this->event->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->getTokenTTl())
            ->willReturn(false)
        ;

        $listener = new PasswordResettingListener($this->getTokenTTl());
        $listener->onResetPasswordStarted($this->event);
    }

    private function getUser(): MockObject
    {
        return $this->getMockBuilder(UserInterface::class)->getMock();
    }

    private function getTokenTTl(): int
    {
        return self::$container->getParameter('api_user.password.resetting.token_ttl');
    }
}
