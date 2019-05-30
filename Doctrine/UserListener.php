<?php

namespace API\UserBundle\Doctrine;

use API\UserBundle\Model\UserInterface;
use API\UserBundle\Util\CanonicalFieldsUpdater;
use API\UserBundle\Util\PasswordUpdaterInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @source https://github.com/FriendsOfSymfony/FOSUserBundle
 */
final class UserListener implements EventSubscriber
{
    private $passwordUpdater;
    private $canonicalFieldsUpdater;

    public function __construct(
        PasswordUpdaterInterface $passwordUpdater,
        CanonicalFieldsUpdater $canonicalFieldsUpdater
    ) {
        $this->passwordUpdater = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof UserInterface) {
            return;
        }

        $this->updateUserFields($object);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof UserInterface) {
            return;
        }

        $this->updateUserFields($object);
        $this->recomputeChangeSet($args->getObjectManager(), $object);
    }

    private function updateUserFields(UserInterface $user)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->passwordUpdater->hashPassword($user);
    }

    private function recomputeChangeSet(ObjectManager $om, UserInterface $user)
    {
        $meta = $om->getClassMetadata(get_class($user));
        $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
    }
}
