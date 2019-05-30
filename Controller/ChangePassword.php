<?php

namespace API\UserBundle\Controller;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Form\Type\ChangePasswordType;
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

final class ChangePassword
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
     *     name="api_user_change_password",
     *     path="/api/change-password",
     *     methods={"POST"},
     *     defaults={
     *          "_api_normalization_context"={"api_sub_level"=true},
     *          "_api_swagger_context"={
     *              "tags"={"User"},
     *              "summary"="Changes password.",
     *              "description"="",
     *              "parameters"={
     *                  {
     *                      "in"="body",
     *                      "schema"={
     *                          "type"="object",
     *                          "properties"={
     *                              "api_user_change_password[currentPassword]"={"type"="string"},
     *                              "api_user_change_password[plainPassword][first]"={"type"="string"},
     *                              "api_user_change_password[plainPassword][second]"={"type"="string"},
     *                          }
     *                      },
     *                      "example"={
     *                          "api_user_change_password[currentPassword]"="oldPassword",
     *                          "api_user_change_password[plainPassword][first]"="test123",
     *                          "api_user_change_password[plainPassword][second]"="test123",
     *                      }
     *                  }
     *              },
     *              "responses"={
     *                  "204"={
     *                      "description"="New password saved.",
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
        $this->passwordRequestHandler->setPassword($user, ChangePasswordType::class);

        try {
            $this->userManager->updateUser($user);

            $this->eventDispatcher->dispatch(
                UserEvent::CHANGE_PASSWORD_COMPLETED,
                new UserEvent($user)
            );

            return new JsonResponse('', Response::HTTP_NO_CONTENT);
        } catch (\PDOException $e) {
            throw new RuntimeException('Changing password failed.', $e->getCode(), $e);
        }
    }
}
