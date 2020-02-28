<?php

declare(strict_types=1);

namespace App\Controller\Status;

use Psl\Str;
use App\Entity\Status;
use App\Repository\StatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/status")
 */
final class DeleteController
{
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UrlGeneratorInterface $urlGenerator;
    private StatusRepository $repository;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, UrlGeneratorInterface $urlGenerator, StatusRepository $repository)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
        $this->repository = $repository;
    }

    /**
     * @IsGranted("STATUS_DELETE", subject="status")
     *
     * @Route("/{id}", name="status_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Status $status): Response
    {
        $token = new CsrfToken(Str\format('%s%s', 'delete', $status->getId()), $request->request->get('_token'));
        if ($this->csrfTokenManager->isTokenValid($token)) {
            $this->repository->delete($status);
        }

        $url = $this->urlGenerator->generate('home');
        return new RedirectResponse($url);
    }
}
