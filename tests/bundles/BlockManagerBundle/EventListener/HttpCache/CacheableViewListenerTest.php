<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CacheableViewListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener
     */
    protected $listener;

    public function setUp()
    {
        $this->listener = new CacheableViewListener();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(
                KernelEvents::VIEW => 'onView',
                KernelEvents::RESPONSE => array('onKernelResponse', -255),
            ),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnView()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);
        $blockView->setMaxAge(24);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $blockView
        );

        $this->listener->onView($event);

        $this->assertEquals(42, $blockView->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnViewWithDisabledCache()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setIsCacheable(false);
        $blockView->setSharedMaxAge(42);
        $blockView->setMaxAge(24);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $blockView
        );

        $this->listener->onView($event);

        $this->assertNull($blockView->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnViewWithOverwritingHeaders()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();

        $blockView->getResponse()->setSharedMaxAge(41);
        $blockView->getResponse()->setMaxAge(23);

        $blockView->setOverwriteHeaders(true);
        $blockView->setSharedMaxAge(42);
        $blockView->setMaxAge(24);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $blockView
        );

        $this->listener->onView($event);

        $this->assertEquals(42, $blockView->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnViewWithoutOverwritingHeaders()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();

        $blockView->getResponse()->setSharedMaxAge(41);
        $blockView->getResponse()->setMaxAge(23);

        $blockView->setSharedMaxAge(42);
        $blockView->setMaxAge(24);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $blockView
        );

        $this->listener->onView($event);

        $this->assertEquals(41, $blockView->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     */
    public function testOnViewWithSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $blockView = new BlockView();
        $blockView->setSharedMaxAge(42);
        $blockView->setMaxAge(24);

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $blockView
        );

        $this->listener->onView($event);

        $this->assertNull($blockView->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onView
     */
    public function testOnViewWithoutSupportedValue()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForControllerResultEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            42
        );

        $this->listener->onView($event);

        $this->assertNull($event->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponse()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layoutView = new LayoutView();
        $layoutView->setSharedMaxAge(42);
        $layoutView->setMaxAge(24);

        $request->attributes->set('layoutView', $layoutView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertEquals(42, $event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponseWithDisabledCache()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layoutView = new LayoutView();
        $layoutView->setIsCacheable(false);
        $layoutView->setSharedMaxAge(42);
        $layoutView->setMaxAge(24);

        $request->attributes->set('layoutView', $layoutView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponseWithOverwritingHeaders()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layoutView = new LayoutView();
        $layoutView->setOverwriteHeaders(true);
        $layoutView->setSharedMaxAge(42);
        $layoutView->setMaxAge(24);

        $request->attributes->set('layoutView', $layoutView);

        $response = new Response();
        $response->setSharedMaxAge(41);
        $response->setMaxAge(23);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $this->listener->onKernelResponse($event);

        $this->assertEquals(42, $event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::setUpCachingHeaders
     */
    public function testOnKernelResponseWithoutOverwritingHeaders()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layoutView = new LayoutView();
        $layoutView->setSharedMaxAge(42);
        $layoutView->setMaxAge(24);

        $request->attributes->set('layoutView', $layoutView);

        $response = new Response();
        $response->setSharedMaxAge(41);
        $response->setMaxAge(23);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $this->listener->onKernelResponse($event);

        $this->assertEquals(41, $event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     */
    public function testOnKernelResponseWithSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $layoutView = new LayoutView();
        $layoutView->setSharedMaxAge(42);
        $layoutView->setMaxAge(24);

        $request->attributes->set('layoutView', $layoutView);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\CacheableViewListener::onKernelResponse
     */
    public function testOnKernelResponseWithoutSupportedValue()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->listener->onKernelResponse($event);

        $this->assertNull($event->getResponse()->getMaxAge());
    }
}