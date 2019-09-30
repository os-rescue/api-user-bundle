<?php

namespace API\UserBundle\Mailer;

use API\UserBundle\Model\UserInterface;

interface MailerInterface
{
    public function sendEmailCreatingConfirmationEmailMessage(UserInterface $user): int;
    public function sendEmailUpdatingConfirmationEmailMessage(UserInterface $user): int;
    public function sendPasswordChangingEmailMessage(UserInterface $user): int;
    public function sendPasswordSettingEmailMessage(UserInterface $user): int;
    public function sendPasswordResettingEmailMessage(UserInterface $user): int;
    public function sendRolePromotingEmailMessage(UserInterface $user): int;
}
