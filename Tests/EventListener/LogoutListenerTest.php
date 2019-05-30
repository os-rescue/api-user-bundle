<?php

namespace API\UserBundle\Tests\EventListener;

use API\UserBundle\EventListener\LogoutListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogoutListenerTest extends TestCase
{
    private $request;
    private $tokenStorage;

    public function setUp()
    {
        $this->request = $this->getMockBuilder(Request::class)->getMock();
        $this->tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
    }

    public function testLogoutSuccess(): void
    {
        $this->tokenStorage->expects($this->once())->method('setToken')->with(null);

        $logoutListener = new LogoutListener($this->tokenStorage);
        $logoutListener->onLogoutSuccess($this->request);
    }
}
