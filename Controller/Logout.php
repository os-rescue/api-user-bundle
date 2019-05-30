<?php

namespace API\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

final class Logout
{
    /**
     * @Route(
     *     name="api_user_logout",
     *     path="/api/logout",
     *     methods={"GET", "POST"},
     *     defaults={
     *          "_api_normalization_context"={"api_sub_level"=true},
     *          "_api_swagger_context"={
     *              "tags"={"User"},
     *              "summary"="Logged out the current user.",
     *              "responses"={
     *                  "204"={
     *                      "User logged out.",
     *                  },
     *              }
     *          }
     *     }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function __invoke()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
