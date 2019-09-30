<?php

namespace API\UserBundle\Event;

use API\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @final
 */
class UserEvent extends Event
{
    public const EMAIL_CREATED = 'api_user.email.created';
    public const EMAIL_UPDATED = 'api_user.email.updated';
    public const EMAIL_CONFIRMATION_INITIALIZE = 'api_user.email.confirmation_initialize';
    public const EMAIL_CONFIRMED = 'api_user.email.updating_confirmed';
    public const CHANGE_PASSWORD_COMPLETED = 'api_user.change_password.completed';
    public const SET_PASSWORD_INITIALIZE = 'api_user.set_password.initialize';
    public const SET_PASSWORD_SUCCESSFUL = 'api_user.set_password.successful';
    public const SET_PASSWORD_COMPLETED = 'api_user.set_password.completed';
    public const RESET_PASSWORD_INITIALIZE = 'api_user.reset_password.initialize';
    public const RESET_PASSWORD_STARTED = 'api_user.reset_password.started';
    public const RESET_PASSWORD_SUCCESSFUL = 'api_user.reset_password.successful';
    public const RESET_PASSWORD_COMPLETED = 'api_user.reset_password.completed';
    public const SENT_MAIL = 'api_user.mail.sent';
    public const USER_DEMOTED = 'api_user.user.demoted';
    public const USER_PROMOTED = 'api_user.user.promoted';

    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
