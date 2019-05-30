<?php

namespace API\UserBundle\Event;

use API\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @final
 */
class GetUserByTokenEvent extends Event
{
    private $token;
    private $user;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }
}
