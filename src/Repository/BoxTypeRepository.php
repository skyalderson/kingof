<?php

namespace App\Repository;

use App\Entity\BoxType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BoxType|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoxType|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoxType[]    findAll()
 * @method BoxType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoxTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoxType::class);
    }

    // /**
    //  * @return BoxType[] Returns an array of BoxType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BoxType
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
