<?php

namespace App\Repository;

use App\Entity\KotCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method KotCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method KotCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method KotCard[]    findAll()
 * @method KotCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KotCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KotCard::class);
    }

    // /**
    //  * @return KotCard[] Returns an array of KotCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?KotCard
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
