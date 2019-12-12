<?php

namespace App\Repository;

use App\Entity\PlayLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlayLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayLog[]    findAll()
 * @method PlayLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayLog::class);
    }

    // /**
    //  * @return PlayLog[] Returns an array of PlayLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlayLog
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
