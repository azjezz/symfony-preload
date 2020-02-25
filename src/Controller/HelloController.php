<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function App\hello;

final class HelloController
{
    /**
     * @Route("/", name="hello")
     */
    public function index(): Response
    {
      return hello();
    }
}
