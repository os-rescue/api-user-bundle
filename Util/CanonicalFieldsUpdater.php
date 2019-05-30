<?php

namespace API\UserBundle\Util;

use API\UserBundle\Model\UserInterface;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 *
 * @final
 */
class CanonicalFieldsUpdater
{
    private $usernameCanonicalizer;
    private $emailCanonicalizer;

    public function __construct(
        CanonicalizerInterface $usernameCanonicalizer,
        CanonicalizerInterface $emailCanonicalizer
    ) {
        $this->usernameCanonicalizer = $usernameCanonicalizer;
        $this->emailCanonicalizer = $emailCanonicalizer;
    }

    public function updateCanonicalFields(UserInterface $user): void
    {
        $user->setUsernameCanonical($this->canonicalizeUsername($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizeEmail($user->getEmail()));
    }

    public function canonicalizeEmail(?string $email): ?string
    {
        return $this->emailCanonicalizer->canonicalize($email);
    }

    public function canonicalizeUsername(?string $username): ?string
    {
        return $this->usernameCanonicalizer->canonicalize($username);
    }
}
