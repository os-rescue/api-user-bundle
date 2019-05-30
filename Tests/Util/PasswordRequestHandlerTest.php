<?php

namespace API\UserBundle\Tests\Util;

use API\UserBundle\Form\Type\SetPasswordType;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Util\PasswordRequestHandler;
use API\UserBundle\Util\ConstraintListConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PasswordRequestHandlerTest extends TestCase
{
    private $constraintListConverter;
    private $form;
    private $formErrorIterator;
    private $formFactory;
    private $formType;
    private $request;
    private $requestStack;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestStack = $this->getMockBuilder(RequestStack::class)->getMock();
        $this->request = $this->getMockBuilder(Request::class)->getMock();

        $this->constraintListConverter = $this->getMockBuilder(ConstraintListConverter::class)->getMock();

        $this->user = $this->getMockBuilder(UserInterface::class)->getMock();

        $this->form = $this->getMockBuilder(FormInterface::class)->getMock();
        $this->formErrorIterator = $this->getMockBuilder(FormErrorIterator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->formType = 'fooType';
        $this->formFactory = $this->getMockBuilder(FormFactoryInterface::class)->getMock();
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->formType, $this->user)
            ->willReturn($this->form)
        ;
        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($this->request)
        ;
        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request)
        ;
    }

    public function testSetPasswordSuccess(): void
    {
        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $setPasswordHandler = $this->getInstance();
        $setPasswordHandler->setPassword($this->user, $this->formType);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Data has not been sent correctly.
     */
    public function testSetPasswordThrowsBadRequestHttpException(): void
    {
        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false)
        ;
        $this->form
            ->expects($this->never())
            ->method('isValid')
        ;

        $setPasswordHandler = $this->getInstance();
        $setPasswordHandler->setPassword($this->user, $this->formType);
    }

    /**
     * @expectedException \ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException
     */
    public function testSetPasswordThrowsValidationException(): void
    {
        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false)
        ;
        $this->form
            ->expects($this->once())
            ->method('getErrors')
            ->willReturn($this->formErrorIterator)
        ;
        $this->constraintListConverter
            ->expects($this->once())
            ->method('convertFormErrorsIntoConstraintList')
            ->with($this->formErrorIterator);

        $setPasswordHandler = $this->getInstance();
        $setPasswordHandler->setPassword($this->user, $this->formType);
    }

    private function getInstance(): PasswordRequestHandler
    {
        return new PasswordRequestHandler(
            $this->requestStack,
            $this->formFactory,
            $this->constraintListConverter
        );
    }
}
