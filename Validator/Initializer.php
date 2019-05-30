<?php

namespace API\UserBundle\Validator;

use API\UserBundle\Model\UserInterface;
use API\UserBundle\Util\CanonicalFieldsUpdater;
use Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
final class Initializer implements ObjectInitializerInterface
{
    private $canonicalFieldsUpdater;
    private $loginCredential;

    public function __construct(CanonicalFieldsUpdater $canonicalFieldsUpdater, string $loginCredential)
    {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
        $this->loginCredential = $loginCredential;
    }

    /**
     * @param object $object
     */
    public function initialize($object)
    {
        if (!$object instanceof UserInterface) {
            return;
        }

        if (null === $object->getUsername() && 'email' === $this->loginCredential) {
            $object->setUsername($object->getEmail());
        }

        $this->canonicalFieldsUpdater->updateCanonicalFields($object);
    }
}
