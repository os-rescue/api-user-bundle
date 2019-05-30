<?php

namespace API\UserBundle\Util;

use API\UserBundle\Model\UserInterface;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
interface PasswordUpdaterInterface
{
    /**
     * Updates the hashed password in the user when there is a new password.
     *
     * The implement should be a no-op in case there is no new password (it should not erase the
     * existing hash with a wrong one).
     */
    public function hashPassword(UserInterface $user): void;
}
