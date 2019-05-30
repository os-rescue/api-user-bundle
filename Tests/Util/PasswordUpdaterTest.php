<?php

namespace API\UserBundle\Tests\Util;

use API\UserBundle\Tests\TestUser;
use API\UserBundle\Util\PasswordUpdater;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class PasswordUpdaterTest extends TestCase
{
    private $passwordUpdater;
    private $encoder;
    private $encoderFactory;

    protected function setUp()
    {
        $this->encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
        $this->encoderFactory = $this->getMockBuilder(EncoderFactoryInterface::class)->getMock();

        $this->passwordUpdater = new PasswordUpdater($this->encoderFactory);
    }

    public function testUpdatePassword(): void
    {
        $user = new TestUser();
        $user->setPlainPassword('password');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->will($this->returnValue($this->encoder));

        $this->encoder->expects($this->once())
            ->method('encodePassword')
            ->with('password', $this->isType('string'))
            ->will($this->returnValue('encodedPassword'));

        $this->passwordUpdater->hashPassword($user);
        $this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNotNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testUpdatePasswordWithBCrypt(): void
    {
        $encoder = $this->getMockBuilder(BCryptPasswordEncoder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $user = new TestUser();
        $user->setPlainPassword('password');
        $user->setSalt('old_salt');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->will($this->returnValue($encoder));

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with('password', $this->isNull())
            ->will($this->returnValue('encodedPassword'));

        $this->passwordUpdater->hashPassword($user);
        $this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testDoesNotUpdateWithoutPlainPassword(): void
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword('');

        $this->passwordUpdater->hashPassword($user);
        $this->assertSame('hash', $user->getPassword());
    }
}
