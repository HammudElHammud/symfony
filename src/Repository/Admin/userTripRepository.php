<?php

namespace App\Repository\Admin;

use App\Entity\Admin\userTrip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method userTrip|null find($id, $lockMode = null, $lockVersion = null)
 * @method userTrip|null findOneBy(array $criteria, array $orderBy = null)
 * @method userTrip[]    findAll()
 * @method userTrip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class userTripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, userTrip::class);
    }

    // /**
    //  * @return userTrip[] Returns an array of userTrip objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?userTrip
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
