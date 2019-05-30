<?php

namespace API\UserBundle\Event;

use API\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @final
 */
class FilterUserResponseEvent extends UserEvent
{
    private $response;

    public function __construct(UserInterface $user)
    {
        parent::__construct($user);
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
