<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class AdminAuthenticationExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // Priority needs to be higher than built in exception listener
        return [KernelEvents::EXCEPTION => ['onException', 20]];
    }

    /**
     * Converts Symfony authentication exceptions to HTTP Access Denied exception.
     */
    public function onException(GetResponseForExceptionEvent $event): void
    {
        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsAdminRequestListener::ADMIN_FLAG_NAME) !== true) {
            return;
        }

        if (!$event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $exception = $event->getException();
        if (!$exception instanceof AuthenticationException && !$exception instanceof AccessDeniedException) {
            return;
        }

        $event->setException(new AccessDeniedHttpException());

        $event->stopPropagation();
    }
}
