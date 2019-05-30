<?php

namespace API\UserBundle\Tests\Security;

use API\UserBundle\Model\UserInterface;
use API\UserBundle\Security\UserChecker;
use PHPUnit\Framework\TestCase;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class UserCheckerTest extends TestCase
{
    private $userChecker;

    protected function setUp()
    {
        parent::setUp();

        $this->userChecker = new UserChecker();
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\LockedException
     * @expectedExceptionMessage User account is locked.
     */
    public function testCheckPreAuthFailsLockedOut(): void
    {
        $userMock = $this->getUser(false, false, false, false);
        $this->userChecker->checkPreAuth($userMock);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\DisabledException
     * @expectedExceptionMessage User account is disabled.
     */
    public function testCheckPreAuthFailsIsEnabled(): void
    {
        $userMock = $this->getUser(true, false, false, false);
        $this->userChecker->checkPreAuth($userMock);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccountExpiredException
     * @expectedExceptionMessage User account has expired.
     */
    public function testCheckPreAuthFailsIsAccountNonExpired(): void
    {
        $userMock = $this->getUser(true, true, false, false);
        $this->userChecker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthSuccess()
    {
        $userMock = $this->getUser(true, true, true, false);

        try {
            $this->assertNull($this->userChecker->checkPreAuth($userMock));
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\CredentialsExpiredException
     * @expectedExceptionMessage User credentials have expired.
     */
    public function testCheckPostAuthFailsIsCredentialsNonExpired(): void
    {
        $userMock = $this->getUser(true, true, true, false);
        $this->userChecker->checkPostAuth($userMock);
    }

    public function testCheckPostAuthSuccess(): void
    {
        $userMock = $this->getUser(true, true, true, true);

        try {
            $this->assertNull($this->userChecker->checkPostAuth($userMock));
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    private function getUser(
        $isAccountNonLocked,
        $isEnabled,
        $isAccountNonExpired,
        $isCredentialsNonExpired
    ): UserInterface {
        $userMock = $this->getMockBuilder('API\UserBundle\Model\User')->getMock();
        $userMock
            ->method('isAccountNonLocked')
            ->willReturn($isAccountNonLocked);
        $userMock
            ->method('isEnabled')
            ->willReturn($isEnabled);
        $userMock
            ->method('isAccountNonExpired')
            ->willReturn($isAccountNonExpired);
        $userMock
            ->method('isCredentialsNonExpired')
            ->willReturn($isCredentialsNonExpired);

        return $userMock;
    }
}
