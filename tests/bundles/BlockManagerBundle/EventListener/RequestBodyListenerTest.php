<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class RequestBodyListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $decoderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener
     */
    private $listener;

    public function setUp()
    {
        $this->decoderMock = $this->createMock(DecoderInterface::class);

        $this->listener = new RequestBodyListener($this->decoderMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::__construct
     */
    public function testOnKernelRequest()
    {
        $this->decoderMock
            ->expects($this->once())
            ->method('decode')
            ->with($this->equalTo('{"test": "value"}'))
            ->will($this->returnValue(array('test' => 'value')));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(
            'value',
            $event->getRequest()->request->get('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithNonApiRoute()
    {
        $this->decoderMock->expects($this->never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $this->decoderMock->expects($this->never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{"test": "value"}');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->headers->set('Content-Type', 'application/json');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::isDecodeable
     */
    public function testOnKernelRequestWithInvalidMethod()
    {
        $this->decoderMock->expects($this->never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::isDecodeable
     */
    public function testOnKernelRequestWithInvalidContentType()
    {
        $this->decoderMock->expects($this->never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{"test": "value"}');
        $request->headers->set('Content-Type', 'some/type');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(
            false,
            $event->getRequest()->attributes->has('test')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Request body has an invalid format
     */
    public function testOnKernelRequestWithInvalidJson()
    {
        $this->decoderMock
            ->expects($this->once())
            ->method('decode')
            ->with($this->equalTo('{]'))
            ->will($this->throwException(new UnexpectedValueException()));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '{]');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Request body has an invalid format
     */
    public function testOnKernelRequestWithNonArrayJson()
    {
        $this->decoderMock
            ->expects($this->once())
            ->method('decode')
            ->with($this->equalTo('42'))
            ->will($this->returnValue(42));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', 'POST', array(), array(), array(), array(), '42');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
