<?php

namespace API\UserBundle\Util;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
interface CanonicalizerInterface
{
    public function canonicalize(?string $string): ?string;
}
