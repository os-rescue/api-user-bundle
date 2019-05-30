<?php

namespace API\UserBundle\Tests\Model;

use API\UserBundle\Model\User;
use API\UserBundle\Model\UserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class UserTest extends TestCase
{
    public function testUsername(): void
    {
        $user = $this->getUser();
        $this->assertNull($user->getUsername());

        $user->setUsername('tony');
        $this->assertSame('tony', $user->getUsername());
    }

    public function testEmail(): void
    {
        $user = $this->getUser();
        $user->setEmail('tony@mail.org');
        $this->assertSame('tony@mail.org', $user->getEmail());
    }

    public function testIsPasswordRequestNonExpired(): void
    {
        $user = $this->getUser();
        $passwordRequestedAt = new \DateTime('-10 seconds');

        $user->setPasswordRequestedAt($passwordRequestedAt);

        $this->assertSame($passwordRequestedAt, $user->getPasswordRequestedAt());
        $this->assertTrue($user->isPasswordRequestNonExpired(15));
        $this->assertFalse($user->isPasswordRequestNonExpired(5));
    }

    public function testIsPasswordRequestAtCleared(): void
    {
        $user = $this->getUser();
        $passwordRequestedAt = new \DateTime('-10 seconds');

        $user->setPasswordRequestedAt($passwordRequestedAt);
        $user->setPasswordRequestedAt(null);

        $this->assertFalse($user->isPasswordRequestNonExpired(15));
        $this->assertFalse($user->isPasswordRequestNonExpired(5));
    }

    public function testTrueHasRole(): void
    {
        $user = $this->getUser();
        $defaultrole = User::ROLE_DEFAULT;
        $newrole = 'ROLE_X';
        $this->assertTrue($user->hasRole($defaultrole));
        $user->addRole($defaultrole);
        $this->assertTrue($user->hasRole($defaultrole));
        $user->addRole($newrole);
        $this->assertTrue($user->hasRole($newrole));
    }

    public function testFalseHasRole(): void
    {
        $user = $this->getUser();
        $newrole = 'ROLE_X';
        $this->assertFalse($user->hasRole($newrole));
        $user->addRole($newrole);
        $this->assertTrue($user->hasRole($newrole));
    }

    public function testIsEqualTo(): void
    {
        $user = $this->getUser();
        $this->assertTrue($user->isEqualTo($user));
        $this->assertFalse($user->isEqualTo($this->getMockBuilder(UserInterface::class)->getMock()));

        $user2 = $this->getUser();
        $user2->setPassword('secret');
        $this->assertFalse($user->isEqualTo($user2));

        $user3 = $this->getUser();
        $user3->setSalt('pepper');
        $this->assertFalse($user->isEqualTo($user3));

        $user4 = $this->getUser();
        $user4->setUsername('f00b4r');
        $this->assertFalse($user->isEqualTo($user4));
    }

    protected function getUser(): MockObject
    {
        return $this->getMockForAbstractClass(User::class);
    }
}
