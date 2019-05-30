<?php

namespace API\UserBundle\Tests\Form\Type;

use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
class ValidatorExtensionTypeTestCase extends TypeTestCase
{
    /**
     * @return array
     */
    protected function getTypeExtensions(): array
    {
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->getMock();
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));

        return [
            new FormTypeValidatorExtension($validator),
        ];
    }
}
