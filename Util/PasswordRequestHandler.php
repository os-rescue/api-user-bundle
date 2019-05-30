<?php

namespace API\UserBundle\Util;

use API\UserBundle\Model\UserInterface;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @final
 */
class PasswordRequestHandler
{
    private $constraintListConverter;
    private $formFactory;
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
        FormFactoryInterface $formFactory,
        ConstraintListConverter $constraintListConverter
    ) {
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
        $this->constraintListConverter = $constraintListConverter;
    }

    public function setPassword(UserInterface $user, string $formType): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->formFactory->create($formType, $user);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            throw new BadRequestHttpException('Data has not been sent correctly.');
        }

        if (!$form->isValid()) {
            throw new ValidationException(
                $this->constraintListConverter->convertFormErrorsIntoConstraintList($form->getErrors(true))
            );
        }
    }
}
