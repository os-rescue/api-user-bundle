<?php

namespace API\UserBundle\Doctrine;

use API\UserBundle\Model\UserInterface;
use API\UserBundle\Model\UserManager as BaseUserManager;
use API\UserBundle\Util\CanonicalFieldsUpdater;
use API\UserBundle\Util\PasswordUpdaterInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
final class UserManager extends BaseUserManager
{
    protected $objectManager;
    private $class;

    public function __construct(
        PasswordUpdaterInterface $passwordUpdater,
        CanonicalFieldsUpdater $canonicalFieldsUpdater,
        ObjectManager $om,
        string $class
    ) {
        parent::__construct($passwordUpdater, $canonicalFieldsUpdater);

        $this->objectManager = $om;
        $this->class = $class;
    }

    public function getClass(): string
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    public function findUserBy(array $criteria): ?UserInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function refreshUser(UserInterface $user): void
    {
        $this->objectManager->refresh($user);
    }

    public function updateUser(UserInterface $user, $andFlush = true): void
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->objectManager->getRepository($this->getClass());
    }
}
