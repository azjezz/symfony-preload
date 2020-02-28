<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController
{
    private AuthenticationUtils $authenticationUtils;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(AuthenticationUtils $authenticationUtils, UrlGeneratorInterface $urlGenerator)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(Session $session): RedirectResponse
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();

        $flash = $session->getFlashBag();
        $flash->add('error', $error ? $error->getMessage() : null);

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
    }
}
