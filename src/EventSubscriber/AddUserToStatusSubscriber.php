<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Status;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddUserToStatusSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['addUser', EventPriorities::PRE_WRITE],
        ];
    }

    public function addUser(ViewEvent $event): void
    {
        $status = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$status instanceof Status || Request::METHOD_POST !== $method) {
            // Only handle Status entities (Event is called on any Api entity)
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $status->setUser($user);
    }
}
