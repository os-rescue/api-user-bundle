<?php

namespace API\UserBundle\Tests\Util;

use API\UserBundle\Tests\TestUser;
use API\UserBundle\Util\CanonicalFieldsUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class CanonicalFieldsUpdaterTest extends TestCase
{
    private $canonicalFieldsUpdater;
    private $usernameCanonicalizer;
    private $emailCanonicalizer;

    protected function setUp()
    {
        $this->usernameCanonicalizer = $this->getMockCanonicalizer();
        $this->emailCanonicalizer = $this->getMockCanonicalizer();

        $this->canonicalFieldsUpdater = new CanonicalFieldsUpdater(
            $this->usernameCanonicalizer,
            $this->emailCanonicalizer
        );
    }

    public function testUpdateCanonicalFields(): void
    {
        $user = new TestUser();
        $user->setUsername('Username');
        $user->setEmail('User@Example.com');

        $this->usernameCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('Username')
            ->will($this->returnCallback('strtolower'));

        $this->emailCanonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('User@Example.com')
            ->will($this->returnCallback('strtolower'));

        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->assertSame('username', $user->getUsernameCanonical());
        $this->assertSame('user@example.com', $user->getEmailCanonical());
    }

    private function getMockCanonicalizer(): MockObject
    {
        return $this->getMockBuilder('API\UserBundle\Util\CanonicalizerInterface')->getMock();
    }
}
