<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetIsAdminRequestListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->listener = new SetIsAdminRequestListener(
            $this->createMock(EventDispatcherInterface::class)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', 30]],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'ngbm_admin_layout_resolver_index');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertTrue(
            $event->getRequest()->attributes->get(SetIsAdminRequestListener::ADMIN_FLAG_NAME)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidRoute(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has(SetIsAdminRequestListener::ADMIN_FLAG_NAME));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has(SetIsAdminRequestListener::ADMIN_FLAG_NAME));
    }
}
