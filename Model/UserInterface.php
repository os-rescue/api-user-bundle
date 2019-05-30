<?php

namespace API\UserBundle\Model;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
interface UserInterface extends \Serializable, BaseUserInterface, EquatableInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';

    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public function getId();
    public function setUsername(?string $username): self;
    public function getUsernameCanonical(): ?string;
    public function setUsernameCanonical(?string $usernameCanonical): self;
    public function setSalt(?string $salt): self;
    public function getEmail(): ?string;
    public function setEmail(?string $email): self;
    public function getEmailCanonical(): ?string;
    public function setEmailCanonical(?string $emailCanonical);
    public function getPlainPassword(): ?string;
    public function setPlainPassword(?string $password): self;
    public function setPassword(?string $password);
    public function getPassword(): ?string;
    public function __toString(): string;
    public function isSuperAdmin(): bool;
    public function setEnabled(bool $boolean): self;
    public function setLocked(bool $boolean): self;
    public function setSuperAdmin(bool $boolean): self;
    public function getConfirmationToken(): ?string;
    public function setConfirmationToken(?string $confirmationToken): self;
    public function setPasswordRequestedAt(\DateTime $date = null): self;
    public function isPasswordRequestNonExpired(int $ttl): bool;
    public function setLastLogin(\DateTime $time = null): self;
    public function hasRole(string $role): bool;
    public function setRoles(array $roles): self;
    public function addRole(string $role): self;
    public function removeRole(string $role): self;
    public function isAccountNonExpired(): bool;
    public function isAccountNonLocked(): bool;
    public function isCredentialsNonExpired(): bool;
    public function isEnabled(): bool;
}
