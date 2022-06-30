<?php

namespace App\Repository\Contrat;

use App\Entity\Contrat\ModeRenouvellement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModeRenouvellement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModeRenouvellement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModeRenouvellement[]    findAll()
 * @method ModeRenouvellement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeRenouvellementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModeRenouvellement::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ModeRenouvellement $entity, bool $flush = true): void
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
    public function remove(ModeRenouvellement $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return ModeRenouvellement[] Returns an array of ModeRenouvellement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModeRenouvellement
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
