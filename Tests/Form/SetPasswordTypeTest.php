<?php

namespace API\UserBundle\Tests\Form\Type;

use API\UserBundle\Form\Type\SetPasswordType;
use API\UserBundle\Tests\TestUser;

class SetPasswordTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit(): void
    {
        $user = new TestUser();

        $form = $this->factory->create(SetPasswordType::class, $user);
        $formData = array(
            'plainPassword' => array(
                'first' => 'test',
                'second' => 'test',
            ),
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($user, $form->getData());
        $this->assertSame('test', $user->getPlainPassword());
    }

    /**
     * @return array
     */
    protected function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            new SetPasswordType('API\UserBundle\Tests\TestUser'),
        ]);
    }
}
