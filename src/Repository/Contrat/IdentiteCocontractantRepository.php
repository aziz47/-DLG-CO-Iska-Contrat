<?php

namespace App\Repository\Contrat;

use App\Entity\Contrat\IdentiteCocontractant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IdentiteCocontractant|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdentiteCocontractant|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdentiteCocontractant[]    findAll()
 * @method IdentiteCocontractant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdentiteCocontractantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdentiteCocontractant::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(IdentiteCocontractant $entity, bool $flush = true): void
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
    public function remove(IdentiteCocontractant $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return IdentiteCocontractant[] Returns an array of IdentiteCocontractant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IdentiteCocontractant
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
