<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Twig\Environment;
use App\Entity\Status;
use App\Form\StatusType;
use Twig\Error\Error as TwigError;
use App\Repository\StatusRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;

final class HomeController
{
    private Environment $environment;
    private FormFactoryInterface $factory;
    private Security $security;
    private StatusRepository $repository;

    public function __construct(Environment $environment, FormFactoryInterface $factory, Security $security, StatusRepository $repository)
    {
        $this->environment = $environment;
        $this->factory = $factory;
        $this->security = $security;
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="home")
     *
     * @throws TwigError
     */
    public function index(Request $request): Response
    {
        $context = [];

        /** @var User $user */
        $user = $this->security->getUser();
        if (null !== $user) {
            $status = new Status($user);
            $form = $this->factory->create(StatusType::class, $status);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->repository->save($status);
            }

            $context['status'] = $status;
            $context['form'] = $form->createView();
        }

        $context['statuses'] = $this->repository->fetch();
        $content = $this->environment->render('home.html.twig', $context);

        return new Response($content);
    }
}
