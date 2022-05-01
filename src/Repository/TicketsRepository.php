<?php

namespace App\Repository;

use App\Entity\Tickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tickets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tickets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tickets[]    findAll()
 * @method Tickets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tickets::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Tickets $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Tickets $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByStatus($status)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :aStatus')
            ->setParameter('aStatus', $status)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getDailyOpenedData()
    {
        return $this->createQueryBuilder('t')
            ->where('MONTH(t.createdAt) = MONTH(NOW())')
            ->andWhere('YEAR(t.createdAt) = YEAR(NOW())')
            ->select('DAY(t.createdAt) AS Dia, COUNT(t.id) as Quantidade')
            ->groupBy('Dia')
            ->orderBy('Dia')
            ->getQuery()
            ->getArrayResult();
    }

    public function getDailyClosedData()
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :aStatus')
            ->setParameter('aStatus', Tickets::STATUS_FINISHED)
            ->andWhere('MONTH(t.createdAt) = MONTH(NOW())')
            ->andWhere('YEAR(t.createdAt) = YEAR(NOW())')
            ->select('DAY(t.createdAt) AS Dia, COUNT(t.id) as Quantidade')
            ->groupBy('Dia')
            ->orderBy('Dia')
            ->getQuery()
            ->getArrayResult();
    }

    public function getMonthlyOpenedData()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('MONTH(t.createdAt) >= 1')
            ->andWhere('YEAR(t.createdAt) = YEAR(NOW())')
            ->select('\'opened\' as status, MONTH(t.createdAt) AS months, COUNT(t.id) as qnty')
            ->groupBy('status, months')
            ->orderBy('months')
            ->getQuery()
            ->getArrayResult();
    }

    public function getMonthlyClosedData()
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :aStatus')
            ->setParameter('aStatus',  Tickets::STATUS_FINISHED)
            ->andWhere('MONTH(t.createdAt) >= 1')
            ->andWhere('YEAR(t.createdAt) = YEAR(NOW())')
            ->select('\'finished\' as status, MONTH(t.closedAt) AS months, COUNT(t.id) as qnty')
            ->groupBy('status, months')
            ->orderBy('months')
            ->getQuery()
            ->getArrayResult();
    }
}
