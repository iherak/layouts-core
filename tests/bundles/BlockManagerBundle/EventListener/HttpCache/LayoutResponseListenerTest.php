<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\Layout\TaggerInterface;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class LayoutResponseListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taggerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener
     */
    protected $listener;

    public function setUp()
    {
        $this->taggerMock = $this->createMock(TaggerInterface::class);

        $this->listener = new LayoutResponseListener($this->taggerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(KernelEvents::RESPONSE => 'onKernelResponse'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponse()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('layoutView', new LayoutView(array('layout' => new Layout())));

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->once())
            ->method('tag')
            ->with($this->equalTo(new Response()), $this->equalTo(new Layout()));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('layoutView', new LayoutView(array('layout' => new Layout())));

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tag');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\LayoutResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithoutSupportedValue()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('layoutView', 42);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tag');

        $this->listener->onKernelResponse($event);
    }
}