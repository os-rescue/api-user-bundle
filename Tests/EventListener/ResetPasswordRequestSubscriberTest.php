<?php

namespace API\UserBundle\Tests\EventListener;

use API\UserBundle\EventListener\ResetPasswordRequestSubscriber;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ResetPasswordRequestSubscriberTest extends KernelTestCase
{
    private $event;
    private $userManager;

    protected function setUp()
    {
        self::bootKernel();

        $this->event = $this->getMockBuilder(GetResponseForControllerResultEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userManager = $this->getMockBuilder(UserManagerInterface::class)->getMock();
    }

    public function testResetPasswordRequestSuccess(): void
    {
        $request = new Request([], [], [], [], [], [], \GuzzleHttp\json_encode(['email' => 'foo']));
        $request->attributes->set('_route', ResetPasswordRequestSubscriber::ROUTE_API_RESET_PASSWORD_REQUEST);

        $user = $this->getUser();

        $this->event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn($user)
        ;
        $user->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(true)
        ;
        $user->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->with($this->getRetryTtl())
            ->willReturn(false)
        ;

        $listener = new ResetPasswordRequestSubscriber($this->userManager, $this->getRetryTtl());
        $listener->onResetPasswordRequest($this->event);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testResetPasswordRequestWithUserNotExist(): void
    {
        $request = new Request([], [], [], [], [], [], \GuzzleHttp\json_encode(['email' => 'foo']));
        $request->attributes->set('_route', ResetPasswordRequestSubscriber::ROUTE_API_RESET_PASSWORD_REQUEST);

        $user = $this->getUser();

        $this->event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn(null)
        ;
        $user->expects($this->never())
            ->method('isAccountNonLocked')
        ;
        $user->expects($this->never())
            ->method('isPasswordRequestNonExpired')
        ;

        $listener = new ResetPasswordRequestSubscriber($this->userManager, $this->getRetryTtl());
        $listener->onResetPasswordRequest($this->event);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testResetPasswordRequestWithAccountLocked(): void
    {
        $request = new Request([], [], [], [], [], [], \GuzzleHttp\json_encode(['email' => 'foo']));
        $request->attributes->set('_route', ResetPasswordRequestSubscriber::ROUTE_API_RESET_PASSWORD_REQUEST);

        $user = $this->getUser();

        $this->event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn($user)
        ;
        $user->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(false)
        ;
        $user->expects($this->never())
            ->method('isPasswordRequestNonExpired')
        ;

        $listener = new ResetPasswordRequestSubscriber($this->userManager, $this->getRetryTtl());
        $listener->onResetPasswordRequest($this->event);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testResetPasswordRequestWithUserHasPasswordRequestedNotExpired(): void
    {
        $request = new Request([], [], [], [], [], [], \GuzzleHttp\json_encode(['email' => 'foo']));
        $request->attributes->set('_route', ResetPasswordRequestSubscriber::ROUTE_API_RESET_PASSWORD_REQUEST);

        $user = $this->getUser();

        $this->event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;
        $this->userManager->expects($this->once())
            ->method('findUserBy')
            ->with(['email' => 'foo'])
            ->willReturn($user)
        ;
        $user->expects($this->once())
            ->method('isAccountNonLocked')
            ->willReturn(true)
        ;
        $user->expects($this->once())
            ->method('isPasswordRequestNonExpired')
            ->willReturn($this->getRetryTtl())
            ->willReturn(true)
        ;

        $listener = new ResetPasswordRequestSubscriber($this->userManager, $this->getRetryTtl());
        $listener->onResetPasswordRequest($this->event);
    }

    private function getUser(): MockObject
    {
        return $this->getMockBuilder(UserInterface::class)->getMock();
    }

    private function getRetryTtl(): string
    {
        return self::$container->getParameter('api_user.password.resetting.retry_ttl');
    }
}
