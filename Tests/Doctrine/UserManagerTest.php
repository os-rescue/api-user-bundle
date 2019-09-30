<?php

namespace API\UserBundle\DependencyInjection;

use API\UserBundle\Doctrine\UserManager;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class UserManagerTest extends TestCase
{
    /** @var UserManager */
    protected $userManager;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        $passwordUpdater = $this->getMockBuilder('API\UserBundle\Util\PasswordUpdaterInterface')->getMock();
        $fieldsUpdater = $this->getMockBuilder('API\UserBundle\Util\CanonicalFieldsUpdater')
            ->disableOriginalConstructor()
            ->getMock();
        $class = $this->getMockBuilder('Doctrine\Common\Persistence\Mapping\ClassMetadata')->getMock();
        $this->om = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')->getMock();

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(TestUser::class))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(TestUser::class))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(TestUser::class));

        $this->userManager = new UserManager($passwordUpdater, $fieldsUpdater, $this->om, TestUser::class);
    }

    public function testGetClass(): void
    {
        $this->assertSame(TestUser::class, $this->userManager->getClass());
    }

    public function testFindUserBy(): void
    {
        $crit = ['foo' => 'bar'];
        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($crit))
            ->will($this->returnValue(null));

        $this->userManager->findUserBy($crit);
    }

    public function testRefreshUser(): void
    {
        $user = $this->getUser();
        $this->om->expects($this->once())
            ->method('refresh')
            ->with($user);

        $this->userManager->refreshUser($user);
    }

    public function testUpdateUser(): void
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('persist')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->updateUser($user);
    }

    /**
     * @return mixed
     */
    protected function getUser(): UserInterface
    {
        return new TestUser();
    }
}
