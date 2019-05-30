<?php

namespace API\UserBundle\Model;

use API\UserBundle\Util\CanonicalFieldsUpdater;
use API\UserBundle\Util\PasswordUpdaterInterface;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
abstract class UserManager implements UserManagerInterface
{
    private $passwordUpdater;
    private $canonicalFieldsUpdater;

    public function __construct(
        PasswordUpdaterInterface $passwordUpdater,
        CanonicalFieldsUpdater $canonicalFieldsUpdater
    ) {
        $this->passwordUpdater = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    public function findUserByConfirmationToken(string $token): ?UserInterface
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    public function updateCanonicalFields(UserInterface $user): void
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
    }

    public function updatePassword(UserInterface $user): void
    {
        $this->passwordUpdater->hashPassword($user);
    }
}
