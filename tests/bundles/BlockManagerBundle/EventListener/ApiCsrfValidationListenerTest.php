<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiCsrfValidationListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidatorInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $csrfTokenValidatorMock;

    /**
     * @var string
     */
    private $csrfTokenId;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->csrfTokenValidatorMock = $this->createMock(
            CsrfTokenValidatorInterface::class
        );

        $this->csrfTokenId = 'token_id';

        $this->listener = new ApiCsrfValidationListener(
            $this->csrfTokenValidatorMock,
            $this->csrfTokenId
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects(self::once())
            ->method('validateCsrfToken')
            ->with(self::identicalTo($request), self::identicalTo($this->csrfTokenId))
            ->will(self::returnValue(true));

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestThrowsAccessDeniedExceptionOnInvalidToken(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Missing or invalid CSRF token');

        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects(self::once())
            ->method('validateCsrfToken')
            ->with(self::identicalTo($request), self::identicalTo($this->csrfTokenId))
            ->will(self::returnValue(false));

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects(self::never())
            ->method('validateCsrfToken');

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInNonApiRequest(): void
    {
        $request = Request::create('/');

        $this->csrfTokenValidatorMock
            ->expects(self::never())
            ->method('validateCsrfToken');

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
