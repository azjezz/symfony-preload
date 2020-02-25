<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\Response;

function hello(): Response
{
  return new Response('Hello, World!');
}
