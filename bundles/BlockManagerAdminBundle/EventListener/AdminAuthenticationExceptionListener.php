<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AdminAuthenticationExceptionListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Priority needs to be higher than built in exception listener
        return array(KernelEvents::EXCEPTION => array('onException', 20));
    }

    /**
     * Converts exceptions to Symfony HTTP exceptions.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
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

        $event->setException(
            new AccessDeniedHttpException()
        );

        $event->stopPropagation();
    }
}