<?php

namespace API\UserBundle\Util;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
interface TokenGeneratorInterface
{
    public function generateToken(): string;
}
