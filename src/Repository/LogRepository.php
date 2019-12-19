<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function findLastLogToDoByGame($idGame, $idPlayer)
    {
        return $this->createQueryBuilder('l')

            ->andWhere('l.game = :val1')
            ->andWhere('l.isDone = 0')
            ->andWhere('l.player = :val2')
            ->setParameter('val1', $idGame)
            ->setParameter('val2', $idPlayer)
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getNewLogs($lastLog, $idGame)
    {
        return $this->createQueryBuilder('l')
            ->select('IDENTITY(l.player) as idPlayer, l.id as idLog, l.action as action')
            ->andWhere('l.game = :val1')
            ->andWhere('l.isDone = 1')
            ->andWhere('l.id > :val2')
            ->setParameter('val1', $idGame)
            ->setParameter('val2', $lastLog)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findFirstLog($idGame)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.game = :val')
            ->setParameter('val', $idGame)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Log[] Returns an array of Log objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Log
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
