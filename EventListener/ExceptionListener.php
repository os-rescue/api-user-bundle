<?php

namespace API\UserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener as BaseExceptionListener;

final class ExceptionListener extends BaseExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if ('html' === $request->getRequestFormat('')) {
            return;
        }

        parent::onKernelException($event);
    }
}
