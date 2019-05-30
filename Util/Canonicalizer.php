<?php

namespace API\UserBundle\Util;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 *
 * @final
 */
class Canonicalizer implements CanonicalizerInterface
{
    public function canonicalize(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        $encoding = mb_detect_encoding($string);
        $result = $encoding
            ? mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, MB_CASE_LOWER);

        return $result;
    }
}
