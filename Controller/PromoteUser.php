<?php

namespace API\UserBundle\Controller;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Model\UserManagerInterface;
use ApiPlatform\Core\Exception\RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PromoteUser extends AbstractController
{
    private $eventDispatcher;
    private $userManager;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UserManagerInterface $userManager
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
    }

    /**
     * @Route(
     *     name="api_user_promote_user",
     *     path="/api/users/{id}/promote-user",
     *     methods={"PUT"},
     *     defaults={
     *          "_api_normalization_context"={"api_sub_level"=true},
     *          "_api_swagger_context"={
     *              "tags"={"User"},
     *              "summary"="Promote user.",
     *              "description"="",
     *              "responses"={
     *                  "204"={
     *                      "description"="User promoted.",
     *                  }
     *              }
     *          }
     *     }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function __invoke(string $id)
    {
        try {
            $user = $this->userManager->findUserBy(['id' => $id]);
            if (!$user) {
                throw $this->createNotFoundException('User not found.');
            }

            $user->setSuperAdmin(true);
            $this->userManager->updateUser($user);

            $this->eventDispatcher->dispatch(
                UserEvent::USER_PROMOTED,
                new UserEvent($user)
            );

            return new JsonResponse('', Response::HTTP_NO_CONTENT);
        } catch (\PDOException $e) {
            throw new RuntimeException('User promotion failed.', $e->getCode(), $e);
        }
    }
}
