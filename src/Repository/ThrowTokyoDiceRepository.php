<?php

namespace App\Repository;

use App\Entity\ThrowTokyoDice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ThrowTokyoDice|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThrowTokyoDice|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThrowTokyoDice[]    findAll()
 * @method ThrowTokyoDice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThrowTokyoDiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThrowTokyoDice::class);
    }

    // /**
    //  * @return ThrowTokyoDice[] Returns an array of ThrowTokyoDice objects
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
    public function findOneBySomeField($value): ?ThrowTokyoDice
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
