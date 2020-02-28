<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Status;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Status|null find($id, $lockMode = null, $lockVersion = null)
 * @method Status|null findOneBy(array $criteria, array $orderBy = null)
 * @method Status[]    findAll()
 * @method Status[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Status[]    findByUser(User $user, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Status::class);
    }

    /**
     * The object will be saved into the database immediately.
     */
    public function save(Status $status): void
    {
        $this->_em->persist($status);
        $this->_em->flush();
    }

    /**
     * Delete an entity instance.
     *
     * A deleted entity will be removed from the database immediately.
     */
    public function delete(Status $status): void
    {
        $this->_em->remove($status);
        $this->_em->flush();
    }

    /**
     * @return Status[]
     */
    public function fetch(): iterable
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }
}
