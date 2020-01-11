<?php

namespace App\Repository;

use App\Entity\KotCardGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method KotCardGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method KotCardGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method KotCardGame[]    findAll()
 * @method KotCardGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KotCardGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KotCardGame::class);
    }

    // /**
    //  * @return KotCardGame[] Returns an array of KotCardGame objects
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
    public function findOneBySomeField($value): ?KotCardGame
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
