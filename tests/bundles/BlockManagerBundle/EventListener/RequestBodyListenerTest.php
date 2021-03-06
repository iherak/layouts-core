<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

final class RequestBodyListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $decoderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->decoderMock = $this->createMock(DecoderInterface::class);

        $this->listener = new RequestBodyListener($this->decoderMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $this->decoderMock
            ->expects(self::once())
            ->method('decode')
            ->with(self::identicalTo('{"test": "value"}'))
            ->will(self::returnValue(['test' => 'value']));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $request = $event->getRequest();

        self::assertTrue($request->attributes->has('data'));

        $data = $event->getRequest()->attributes->get('data');
        self::assertInstanceOf(ParameterBag::class, $data);

        self::assertSame('value', $data->get('test'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithNonApiRoute(): void
    {
        $this->decoderMock->expects(self::never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'application/json');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $this->decoderMock->expects(self::never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->headers->set('Content-Type', 'application/json');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::isDecodeable
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidMethod(): void
    {
        $this->decoderMock->expects(self::never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::isDecodeable
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidContentType(): void
    {
        $this->decoderMock->expects(self::never())->method('decode');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{"test": "value"}');
        $request->headers->set('Content-Type', 'some/type');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('data'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidJson(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Request body has an invalid format');

        $this->decoderMock
            ->expects(self::once())
            ->method('decode')
            ->with(self::identicalTo('{]'))
            ->will(self::throwException(new UnexpectedValueException()));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '{]');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\RequestBodyListener::onKernelRequest
     */
    public function testOnKernelRequestWithNonArrayJson(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Request body has an invalid format');

        $this->decoderMock
            ->expects(self::once())
            ->method('decode')
            ->with(self::identicalTo('42'))
            ->will(self::returnValue(42));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/', Request::METHOD_POST, [], [], [], [], '42');
        $request->headers->set('Content-Type', 'application/json');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
