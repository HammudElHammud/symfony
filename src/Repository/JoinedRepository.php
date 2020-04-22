<?php

namespace App\Repository;

use App\Entity\Joined;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Joined|null find($id, $lockMode = null, $lockVersion = null)
 * @method Joined|null findOneBy(array $criteria, array $orderBy = null)
 * @method Joined[]    findAll()
 * @method Joined[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoinedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Joined::class);
    }

    // /**
    //  * @return Joined[] Returns an array of Joined objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Joined
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
