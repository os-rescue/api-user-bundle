<?php

namespace API\UserBundle\Tests\Common;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;

class PHPUnitTestEmailListener implements TestListener
{
    use TestListenerDefaultImplementation;

    public function startTest(Test $test): void
    {
        TestEmailListener::reset();
    }
}
