<?php

namespace API\UserBundle\Model;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 *
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={
 *              "groups"={
 *                  "item_user_normalized",
 *                  "admin_item_user_normalized",
 *                  "collection_users_normalized"
 *              }
 *          },
 *          "denormalization_context"={
 *              "groups"={"user_model"},
 *              "allow_extra_attributes"=false,
 *              "datetime_format"="Y-m-d\TH:i:sZ",
 *          }
 *     }
 * )
 */
abstract class User implements UserInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string The email of the user.
     *
     * @ORM\Column(name="email", type="string", length=180)
     * @Assert\Email(mode="strict", message="invalid")
     * @Assert\Length(
     *     min = 2,
     *     max = 180,
     *     minMessage = "length.min,{{ limit }}",
     *     maxMessage = "length.max,{{ limit }}",
     * )
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     * @Groups({"user_model", "item_user_normalized", "collection_users_normalized"})
     */
    protected $email;

    /**
     * @ORM\Column(name="email_canonical", type="string", length=180)
     */
    protected $emailCanonical;

    /**
     * @var string The username of the user.
     *
     * @ORM\Column(name="username", type="string", length=180)
     * @Assert\Type("string")
     * @Assert\Length(
     *     min = 2,
     *     max = 180,
     *     minMessage = "length.min,{{ limit }}",
     *     maxMessage = "length.max,{{ limit }}",
     * )
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     * @Groups({"user_model"})
     */
    protected $username;

    /**
     * @ORM\Column(name="username_canonical", type="string", length=180)
     */
    protected $usernameCanonical;

    /**
     * @ORM\Column(name="enabled", type="boolean", options={"default": false})
     * @Groups({"admin_item_user_normalized", "collection_users_normalized"})
     */
    protected $enabled;

    /**
     * @ORM\Column(name="locked", type="boolean", options={"default": false})
     * @Groups({"admin_item_user_normalized", "collection_users_normalized"})
     */
    protected $locked;

    /**
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    protected $salt;

    /**
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    protected $password;

    /**
     * @Assert\NotNull(message="not_null", groups={"SettingPassword"})
     * @Assert\NotBlank(message="not_blank", groups={"SettingPassword"})
     * @PasswordRequirements(
     *     minLength = 8,
     *     requireCaseDiff = true,
     *     requireNumbers = true,
     *     requireSpecialCharacter = true,
     *     tooShortMessage = "length.min,{{length}}",
     *     missingLettersMessage = "require_at_least_one_letter",
     *     requireCaseDiffMessage = "require_both_upper_lower_case_letters",
     *     missingNumbersMessage = "require_at_least_one_number",
     *     missingSpecialCharacterMessage = "require_at_least_one_special_character",
     *     groups={"SettingPassword"}
     * )
     * @Groups({"user_model"})
     */
    protected $plainPassword;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @Groups({"item_user_normalized", "collection_users_normalized"})
     */
    protected $lastLogin;

    /**
     * @ORM\Column(name="confirmation_token", type="string", length=180, unique=true, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @ORM\Column(name="roles", type="array")
     * @Groups({"user_model", "admin_item_user_normalized", "collection_users_normalized"})
     */
    protected $roles;

    public function __construct()
    {
        $this->enabled = false;
        $this->roles = array();
        $this->locked = false;
    }

    public function __toString(): string
    {
        return (string) $this->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function addRole(string $role): UserInterface
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        if (13 === count($data)) {
            // Unserializing a User object from 1.3.x
            unset($data[4], $data[5], $data[6], $data[9], $data[10]);
            $data = array_values($data);
        } elseif (11 === count($data)) {
            // Unserializing a User from a dev version somewhere between 2.0-alpha3 and 2.0-beta1
            unset($data[4], $data[7], $data[8]);
            $data = array_values($data);
        }

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
        ) = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function getUsernameCanonical(): string
    {
        return $this->usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;

        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function isAccountNonExpired(): bool
    {
        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return !$this->locked;
    }

    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function removeRole(string $role): UserInterface
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function setUsername(?string $username): UserInterface
    {
        $this->username = $username;

        return $this;
    }

    public function setUsernameCanonical(?string $usernameCanonical): UserInterface
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    public function setSalt(?string $salt): UserInterface
    {
        $this->salt = $salt;

        return $this;
    }

    public function setEmail(?string $email): UserInterface
    {
        $this->email = $email;

        return $this;
    }

    public function setEmailCanonical(?string $emailCanonical): UserInterface
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function setEnabled(bool $boolean): UserInterface
    {
        $this->enabled = $boolean;

        return $this;
    }

    public function setLocked(bool $boolean): UserInterface
    {
        $this->locked = $boolean;

        return $this;
    }

    public function setPassword(?string $password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    public function setSuperAdmin(bool $boolean): UserInterface
    {
        return $boolean ?
            $this->addRole(static::ROLE_SUPER_ADMIN) :
            $this->removeRole(static::ROLE_SUPER_ADMIN)
            ;
    }

    public function setPlainPassword(?string $password): UserInterface
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function setLastLogin(\DateTime $time = null): UserInterface
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function setConfirmationToken(?string $confirmationToken): UserInterface
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function setPasswordRequestedAt(\DateTime $date = null): UserInterface
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function isPasswordRequestNonExpired(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function setRoles(array $roles): UserInterface
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(BaseUserInterface $user)
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
