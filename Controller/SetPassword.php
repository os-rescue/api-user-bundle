<?php

namespace API\UserBundle\Controller;

use API\UserBundle\Event\FilterUserResponseEvent;
use API\UserBundle\Form\Type\SetPasswordType;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Util\PasswordRequestHandler;
use ApiPlatform\Core\Exception\RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SetPassword
{
    private $eventDispatcher;
    private $tokenStorage;
    private $userManager;
    private $passwordRequestHandler;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface $tokenStorage,
        UserManagerInterface $userManager,
        PasswordRequestHandler $passwordRequestHandler
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
        $this->passwordRequestHandler = $passwordRequestHandler;
    }

    /**
     * @Route(
     *     name="api_user_set_password",
     *     path="/api/set-password",
     *     methods={"POST"},
     *     defaults={
     *          "_api_normalization_context"={"api_sub_level"=true},
     *          "_api_swagger_context"={
     *              "tags"={"User"},
     *              "summary"="Set the password the new password.",
     *              "description"="After the confirmation of the user account,
     *                             a token will be sent back to the client and it will be used to set the password.",
     *              "parameters"={
     *                  {
     *                      "in"="body",
     *                      "schema"={
     *                          "type"="object",
     *                          "properties"={
     *                              "api_user_set_password[plainPassword][first]"={"type"="string"},
     *                              "api_user_set_password[plainPassword][second]"={"type"="string"},
     *                          }
     *                      },
     *                      "example"={
     *                          "api_user_set_password[plainPassword][first]"="test123",
     *                          "api_user_set_password[plainPassword][second]"="test123",
     *                      }
     *                  }
     *              },
     *              "responses"={
     *                  "200"={
     *                      "description"="Password defined.",
     *                  },
     *                  "400"={
     *                      "description"="Bad Request.",
     *                  }
     *              }
     *          }
     *     }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function __invoke(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $this->passwordRequestHandler->setPassword($user, SetPasswordType::class);

        $event = new FilterUserResponseEvent($user);
        $this->eventDispatcher->dispatch(
            FilterUserResponseEvent::SET_PASSWORD_SUCCESSFUL,
            $event
        );

        try {
            $this->userManager->updateUser($user);

            $this->eventDispatcher->dispatch(
                FilterUserResponseEvent::SET_PASSWORD_COMPLETED,
                $event
            );

            return new JsonResponse('', Response::HTTP_NO_CONTENT);
        } catch (\PDOException $e) {
            throw new RuntimeException('Setting the password failed.', $e->getCode(), $e);
        }
    }
}
