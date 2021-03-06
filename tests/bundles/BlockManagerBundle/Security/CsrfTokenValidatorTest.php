<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Security;

use Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class CsrfTokenValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $csrfTokenManagerMock;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $sessionMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator
     */
    private $validator;

    public function setUp(): void
    {
        $this->csrfTokenManagerMock = $this->createMock(
            CsrfTokenManagerInterface::class
        );

        $this->sessionMock = $this->createMock(SessionInterface::class);

        $this->validator = new CsrfTokenValidator(
            $this->csrfTokenManagerMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfToken(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::once())
            ->method('isTokenValid')
            ->with(self::equalTo(new CsrfToken('token_id', 'token')))
            ->will(self::returnValue(true));

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->will(self::returnValue(true));

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenOnInvalidToken(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::once())
            ->method('isTokenValid')
            ->with(self::equalTo(new CsrfToken('token_id', 'token')))
            ->will(self::returnValue(false));

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->will(self::returnValue(true));

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->setSession($this->sessionMock);

        self::assertFalse($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenOnMissingTokenHeader(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->will(self::returnValue(true));

        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->setSession($this->sessionMock);

        self::assertFalse($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenWithNotStartedSession(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->will(self::returnValue(false));

        $request = Request::create('/');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenWithNoSession(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::never())
            ->method('isStarted');

        $request = Request::create('/');

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidator::validateCsrfToken
     */
    public function testValidateCsrfTokenWithSafeMethod(): void
    {
        $this->csrfTokenManagerMock
            ->expects(self::never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects(self::once())
            ->method('isStarted')
            ->will(self::returnValue(true));

        $request = Request::create('/');
        $request->setSession($this->sessionMock);

        self::assertTrue($this->validator->validateCsrfToken($request, 'token_id'));
    }
}
