<?php

namespace API\UserBundle\Tests;

use API\UserBundle\Model\User;

class TestUser extends User
{
    /**
     * @param $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}
