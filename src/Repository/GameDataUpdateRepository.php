<?php

namespace App\Repository;

use App\Entity\GameDataUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GameDataUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameDataUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameDataUpdate[]    findAll()
 * @method GameDataUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameDataUpdateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameDataUpdate::class);
    }

    // /**
    //  * @return GameDataUpdate[] Returns an array of GameDataUpdate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GameDataUpdate
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
