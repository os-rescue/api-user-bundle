<?php

namespace API\UserBundle\Tests\Form\Type;

use API\UserBundle\Form\Type\ChangePasswordType;
use API\UserBundle\Tests\TestUser;

class ChangePasswordTypeTest extends ValidatorExtensionTypeTestCase
{
    public function testSubmit(): void
    {
        $user = new TestUser();
        $user->setPassword('foo');

        $form = $this->factory->create(ChangePasswordType::class, $user);
        $formData = array(
            'currentPassword' => 'foo',
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
        return array_merge(parent::getTypes(), [new ChangePasswordType('API\UserBundle\Tests\TestUser')]);
    }
}
