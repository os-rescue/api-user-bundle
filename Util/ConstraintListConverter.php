<?php

namespace API\UserBundle\Util;

use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintListConverter
{
    public function convertViolationListToArray(
        ConstraintViolationListInterface $violationsList,
        $propertyPath = null
    ): array {
        $output = [];

        foreach ($violationsList as $violation) {
            $output[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        if (null !== $propertyPath) {
            if (\array_key_exists($propertyPath, $output)) {
                $output = [$propertyPath => $output[$propertyPath]];
            } else {
                return [];
            }
        }

        return $output;
    }

    public function convertFormErrorsIntoConstraintList(FormErrorIterator $errors): ConstraintViolationListInterface
    {
        $result = new ConstraintViolationList();
        foreach ($errors as $error) {
            if (!$error->getCause()) {
                continue;
            }

            $result->add($error->getCause());
        }

        return $result;
    }
}
