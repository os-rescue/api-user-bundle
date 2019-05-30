<?php

namespace API\UserBundle\Tests\Util;

use API\UserBundle\Util\ConstraintListConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintListConverterTest extends TestCase
{
    private $converter;

    public function setUp()
    {
        parent::setUp();

        $this->converter = new ConstraintListConverter();
    }

    public function testConvertViolationsListToArray(): void
    {
        $errorsAsArray = $this->converter->convertViolationListToArray(
            $this->getConstraintViolationList()
        );

        $this->assertSame([
            'foo' => [
                'not_blank'
            ],
            'bar' => [
                'not_blank'
            ],
            'foobar' => [
                'not_null'
            ]
        ], $errorsAsArray);
    }

    public function testConvertFormErrorsIntoConstraintList(): void
    {
        $form = $this->getMockBuilder(FormInterface::class)
            ->getMock()
        ;

        $constraints = $this->converter->convertFormErrorsIntoConstraintList(
            new FormErrorIterator($form, $this->getErrors())
        );

        $this->assertCount(3, $constraints);
        $this->assertEquals($this->getConstraintViolationList(), $constraints);
    }

    private function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return new ConstraintViolationList([
            new ConstraintViolation(
                'not_blank',
                'error_template',
                [],
                null,
                'foo',
                ''
            ),
            new ConstraintViolation(
                'not_blank',
                'error_template',
                [],
                null,
                'bar',
                ''
            ),
            new ConstraintViolation(
                'not_null',
                'error_template',
                [],
                null,
                'foobar',
                ''
            )
        ]);
    }

    private function getErrors(): array
    {
        $errors = [];
        $violationList = $this->getConstraintViolationList();

        foreach ($violationList as $violation) {
            $errors[] = new FormError('', 'error_template', [], null, $violation);
        }

        return $errors;
    }
}
