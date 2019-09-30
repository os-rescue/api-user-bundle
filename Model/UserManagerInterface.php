<?php

namespace API\UserBundle\Model;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
interface UserManagerInterface
{
    public function getClass(): string;
    public function findUserBy(array $criteria): ?UserInterface;
    public function refreshUser(UserInterface $user): void;
    public function updateUser(UserInterface $user): void;
    public function findUserByConfirmationToken(string $token): ?UserInterface;
    public function updateCanonicalFields(UserInterface $user): void;
    public function updatePassword(UserInterface $user): void;
}
