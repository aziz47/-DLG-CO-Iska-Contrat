<?php

namespace App\Repository;

use App\Entity\UserJuridiqueData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserJuridiqueData>
 *
 * @method UserJuridiqueData|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserJuridiqueData|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserJuridiqueData[]    findAll()
 * @method UserJuridiqueData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserJuridiqueDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserJuridiqueData::class);
    }

    public function add(UserJuridiqueData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserJuridiqueData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserJuridiqueData[] Returns an array of UserJuridiqueData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserJuridiqueData
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
