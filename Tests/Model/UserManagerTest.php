<?php

namespace API\UserBundle\Tests\Model;

use API\UserBundle\Model\User;
use API\UserBundle\Model\UserManager;
use API\UserBundle\Util\CanonicalFieldsUpdater;
use API\UserBundle\Util\PasswordUpdaterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    private $manager;
    private $passwordUpdater;
    private $fieldsUpdater;

    protected function setUp()
    {
        $this->passwordUpdater = $this->getMockBuilder(PasswordUpdaterInterface::class)->getMock();
        $this->fieldsUpdater = $this->getMockBuilder(CanonicalFieldsUpdater::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = $this->getUserManager([
            $this->passwordUpdater,
            $this->fieldsUpdater,
        ]);
    }

    public function testUpdateCanonicalFields(): void
    {
        $user = $this->getUser();

        $this->fieldsUpdater->expects($this->once())
            ->method('updateCanonicalFields')
            ->with($this->identicalTo($user));

        $this->manager->updateCanonicalFields($user);
    }

    public function testUpdatePassword(): void
    {
        $user = $this->getUser();

        $this->passwordUpdater->expects($this->once())
            ->method('hashPassword')
            ->with($this->identicalTo($user));

        $this->manager->updatePassword($user);
    }

    public function testFindUserByConfirmationToken(): void
    {
        $this->manager->expects($this->once())
            ->method('findUserBy')
            ->with($this->equalTo(array('confirmationToken' => 'foo')));

        $this->manager->findUserByConfirmationToken('foo');
    }

    private function getUser(): MockObject
    {
        return $this->getMockBuilder(User::class)
            ->getMockForAbstractClass();
    }

    private function getUserManager(array $args): MockObject
    {
        return $this->getMockBuilder(UserManager::class)
            ->setConstructorArgs($args)
            ->getMockForAbstractClass();
    }
}
