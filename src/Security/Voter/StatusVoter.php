<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Psl\Arr;
use App\Entity\Status;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class StatusVoter extends Voter
{
    public const EditAttribute = 'STATUS_EDIT';
    public const DeleteAttribute = 'STATUS_DELETE';

    public const Attributes = [
        self::EditAttribute,
        self::DeleteAttribute,
    ];

    /**
     * @param string $attribute
     * @param mixed  $subject
     */
    protected function supports($attribute, $subject): bool
    {
        return Arr\contains(self::Attributes, $attribute) &&
            $subject instanceof Status;
    }

    /**
     * @param string      $attribute
     * @param Status|null $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (Arr\contains(self::Attributes, $attribute)) {
            return null !== $subject && $user === $subject->getUser();
        }

        return false;
    }
}
