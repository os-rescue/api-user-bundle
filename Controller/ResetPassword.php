<?php

namespace API\UserBundle\Controller;

use API\UserBundle\Event\FilterUserResponseEvent;
use API\UserBundle\Event\GetUserByTokenEvent;
use API\UserBundle\Event\UserEvent;
use API\UserBundle\Form\Type\SetPasswordType;
use API\UserBundle\Model\UserManagerInterface;
use API\UserBundle\Util\PasswordRequestHandler;
use ApiPlatform\Core\Exception\RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ResetPassword
{
    private $eventDispatcher;
    private $userManager;
    private $passwordRequestHandler;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserManagerInterface $userManager,
        PasswordRequestHandler $passwordRequestHandler
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
        $this->passwordRequestHandler = $passwordRequestHandler;
    }

    /**
     * @Route(
     *     name="api_user_reset_password",
     *     path="/api/reset-password/{token}",
     *     methods={"POST"},
     *     defaults={
     *          "_api_normalization_context"={"api_sub_level"=true},
     *          "_api_swagger_context"={
     *              "tags"={"User"},
     *              "summary"="Reset the password.",
     *              "description"="Resetting password requires a valid token.",
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
     *                      "description"="Password changed.",
     *                  },
     *                  "400"={
     *                      "description"="Bad Request.",
     *                  }
     *              }
     *          }
     *     }
     * )
     */
    public function __invoke(Request $request, string $token)
    {
        $tokenEvent = new GetUserByTokenEvent($token);
        $this->eventDispatcher->dispatch(
            UserEvent::RESET_PASSWORD_INITIALIZE,
            $tokenEvent
        );

        $user = $tokenEvent->getUser();
        $event = new FilterUserResponseEvent($user);
        $this->eventDispatcher->dispatch(
            FilterUserResponseEvent::RESET_PASSWORD_STARTED,
            $event
        );

        $this->passwordRequestHandler->setPassword($user, SetPasswordType::class);

        $this->eventDispatcher->dispatch(
            FilterUserResponseEvent::RESET_PASSWORD_SUCCESSFUL,
            $event
        );

        try {
            $this->userManager->updateUser($user);

            $this->eventDispatcher->dispatch(
                FilterUserResponseEvent::RESET_PASSWORD_COMPLETED,
                $event
            );

            if (null === $response = $event->getResponse()) {
                throw new RuntimeException('The authentication process is failed.');
            }

            return $response;
        } catch (\PDOException $e) {
            throw new RuntimeException('Resetting password failed.', $e->getCode(), $e);
        }
    }
}
