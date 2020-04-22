<?php

namespace App\Repository\Admin;

use App\Entity\Admin\trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method trip[]    findAll()
 * @method trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class tripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, trip::class);
    }

    // /**
    //  * @return trip[] Returns an array of trip objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?trip
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
