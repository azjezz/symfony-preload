<?php

declare(strict_types=1);

namespace App\Command;

use Psl\Str;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserCreateCommand extends Command
{
    private UserRepository $repository;
    private UserPasswordEncoderInterface $encoder;
    private ValidatorInterface $validator;

    public function __construct(UserRepository $repository, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->encoder = $encoder;
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:create')
            ->setDescription('Create a new user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();

        do {
            /** @var string $username */
            $username = $io->ask('Enter your username ');
            /** @var string $password */
            $password = $io->askHidden('Enter your password ');

            $user->setUsername($username);
            $user->setPlainPassword($password);

            $violations = $this->validator->validate($user);
            if (0 !== $violations->count()) {
                /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
                foreach ($violations as $violation) {
                    $property = 'username' === $violation->getPropertyPath() ? 'username' : 'password';
                    /** @var string $message */
                    $message = $violation->getMessage();
                    $io->error(Str\format('Invalid %s : %s', $property, $message));
                }
            }
        } while (0 !== $violations->count());

        $hash = $this->encoder->encodePassword($user, (string)$user->getPlainPassword());
        $user->setPassword($hash);
        $user->eraseCredentials();

        $this->repository->save($user);

        $io->success(Str\format('User %s has been successfully created.', $user->getUsername()));

        return 0;
    }
}
