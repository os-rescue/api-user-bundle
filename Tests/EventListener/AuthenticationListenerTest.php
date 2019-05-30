<?php

namespace API\UserBundle\Tests\EventListener;

use API\UserBundle\Event\FilterUserResponseEvent;
use API\UserBundle\EventListener\AuthenticationListener;
use API\UserBundle\Model\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class AuthenticationListenerTest extends TestCase
{
    /** @var AuthenticationSuccessHandler */
    private $authenticationSuccessHandler;

    /** @var FilterUserResponseEvent */
    private $event;

    /** @var AuthenticationListener */
    private $listener;

    /** @var UserInterface */
    private $user;

    /** @var UserCheckerInterface */
    private $userChecker;

    public function setUp()
    {
        $this->user = $this->getMockBuilder(UserInterface::class)->getMock();

        $this->event = $this->getMockBuilder(FilterUserResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->event->expects($this->once())->method('getUser')->willReturn($this->user);

        $this->authenticationSuccessHandler = $this->getMockBuilder(AuthenticationSuccessHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userChecker = $this->getMockBuilder(UserCheckerInterface::class)->getMock();

        $this->listener = new AuthenticationListener($this->authenticationSuccessHandler, $this->userChecker);
    }

    public function testAuthenticateSuccess(): void
    {
        $response = new JWTAuthenticationSuccessResponse('foo');
        $this->authenticationSuccessHandler
            ->expects($this->once())
            ->method('handleAuthenticationSuccess')
            ->with($this->user)
            ->willReturn($response);

        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(JWTAuthenticationSuccessResponse::class));

        $this->listener->authenticate($this->event);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccountStatusException
     */
    public function testAuthenticateFailed(): void
    {
        $this->userChecker
            ->expects($this->once())
            ->method('checkPreAuth')
            ->with($this->user)
            ->will($this->throwException(new LockedException()));

        $this->listener->authenticate($this->event);
    }
}
