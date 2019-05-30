<?php

namespace API\UserBundle\Tests\Common;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;

class PHPUnitTestEventDispatcherListener implements TestListener
{
    use TestListenerDefaultImplementation;

    public function startTest(Test $test): void
    {
        TestEventDispatcher::resetEvents();
    }
}
