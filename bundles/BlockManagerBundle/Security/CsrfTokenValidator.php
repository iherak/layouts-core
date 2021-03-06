<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class CsrfTokenValidator implements CsrfTokenValidatorInterface
{
    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function validateCsrfToken(Request $request, string $csrfTokenId): bool
    {
        // Skip CSRF validation if no session is available
        if (!$request->hasSession()) {
            return true;
        }

        $session = $request->getSession();
        if (!$session instanceof SessionInterface || !$session->isStarted()) {
            return true;
        }

        if ($request->isMethodSafe(false)) {
            return true;
        }

        if (!$request->headers->has(self::CSRF_TOKEN_HEADER)) {
            return false;
        }

        $token = new CsrfToken(
            $csrfTokenId,
            $request->headers->get(self::CSRF_TOKEN_HEADER)
        );

        return $this->csrfTokenManager->isTokenValid($token);
    }
}
