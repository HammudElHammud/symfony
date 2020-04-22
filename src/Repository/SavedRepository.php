<?php

namespace App\Repository;

use App\Entity\Saved;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Saved|null find($id, $lockMode = null, $lockVersion = null)
 * @method Saved|null findOneBy(array $criteria, array $orderBy = null)
 * @method Saved[]    findAll()
 * @method Saved[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SavedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Saved::class);
    }

    // /**
    //  * @return Saved[] Returns an array of Saved objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Saved
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
