<?php

namespace API\UserBundle\Tests\Controller;

use API\UserBundle\Controller\SetPassword;
use API\UserBundle\Event\UserEvent;
use API\UserBundle\Form\Type\SetPasswordType;
use API\UserBundle\Tests\Common\EventDispatcherTestCase;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Util\PasswordRequestHandler;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SetPasswordTest extends EventDispatcherTestCase
{
    private $user;
    private $userManager;
    private $request;
    private $tokenStorage;
    private $passwordRequestHandler;
    private $validationException;

    protected function setUp()
    {
        parent::setUp();

        $tokenInterface = $this->getMockBuilder(TokenInterface::class)->getMock();
        $this->tokenStorage = $this->getMockBuilder(TokenStorage::class)->getMock();

        $this->user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->userManager = $this->getMockBuilder(UserManagerInterface::class)->getMock();

        $this->request = $this->getMockBuilder(Request::class)->getMock();

        $this->passwordRequestHandler = $this->getMockBuilder(PasswordRequestHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $constraintViolationList = $this->getMockBuilder(ConstraintViolationListInterface::class)->getMock();
        $this->validationException = new ValidationException($constraintViolationList);

        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenInterface);

        $tokenInterface
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);
    }

    public function testSetPasswordSuccess(): void
    {
        $this->passwordRequestHandler
            ->expects($this->once())
            ->method('setPassword')
            ->with($this->user, SetPasswordType::class)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->with($this->user)
        ;

        $changePasswordController = $this->getInstance();
        $changePasswordController($this->request);

        $this->assertDispatchedUserEvent(UserEvent::SET_PASSWORD_SUCCESSFUL, 1);
        $this->assertDispatchedUserEvent(UserEvent::SET_PASSWORD_COMPLETED, 1);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testSetPasswordWithoutContent(): void
    {
        $this->passwordRequestHandler
            ->expects($this->once())
            ->method('setPassword')
            ->with($this->user, SetPasswordType::class)
            ->willThrowException(new BadRequestHttpException())
        ;
        $this->userManager
            ->expects($this->never())
            ->method('updateUser')
        ;

        $changePasswordController = $this->getInstance();
        $changePasswordController($this->request);

        $this->assertDispatchedUserEvent(UserEvent::SET_PASSWORD_SUCCESSFUL, 0);
        $this->assertDispatchedUserEvent(UserEvent::CHANGE_PASSWORD_COMPLETED, 0);
    }

    /**
     * @expectedException \ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException
     */
    public function testSetPasswordWithInvalidPassword(): void
    {
        $this->passwordRequestHandler
            ->expects($this->once())
            ->method('setPassword')
            ->with($this->user, SetPasswordType::class)
            ->willThrowException($this->validationException)
        ;
        $this->userManager
            ->expects($this->never())
            ->method('updateUser')
        ;

        $changePasswordController = $this->getInstance();
        $changePasswordController($this->request);

        $this->assertDispatchedUserEvent(UserEvent::SET_PASSWORD_SUCCESSFUL, 0);
        $this->assertDispatchedUserEvent(UserEvent::CHANGE_PASSWORD_COMPLETED, 0);
    }

    /**
     * @expectedException \ApiPlatform\Core\Exception\RuntimeException
     */
    public function testSetPasswordWithInvalidContent(): void
    {
        $this->passwordRequestHandler
            ->expects($this->once())
            ->method('setPassword')
            ->with($this->user, SetPasswordType::class)
        ;
        $this->userManager
            ->expects($this->once())
            ->method('updateUser')
            ->willThrowException(new \PDOException())
        ;

        $changePasswordController = $this->getInstance();
        $changePasswordController($this->request);

        $this->assertDispatchedUserEvent(UserEvent::SET_PASSWORD_SUCCESSFUL, 1);
        $this->assertDispatchedUserEvent(UserEvent::CHANGE_PASSWORD_COMPLETED, 0);
    }

    private function getInstance(): SetPassword
    {
        return new SetPassword(
            $this->eventDispatcher,
            $this->tokenStorage,
            $this->userManager,
            $this->passwordRequestHandler
        );
    }
}
