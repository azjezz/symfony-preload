<?php

declare(strict_types=1);

namespace App\Security;

use Psl\Str;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

final class UserProvider implements UserProviderInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username): User
    {
        $user = $this->userRepository->findOneByUsername($username);

        if (null === $user) {
            throw new UsernameNotFoundException(Str\format('User "%s" not found.', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): User
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(Str\format('User "%s" is unsupported.', \get_class($user)));
        }

        $id = $user->getId();
        $refreshedUser = $this->userRepository->find($id);

        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(Str\format('User with id "%s" not found', $id));
        }

        return $refreshedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
