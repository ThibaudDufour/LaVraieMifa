<?php

namespace App\Repository;

use App\Entity\TracaMailAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TracaMailAdmin>
 *
 * @method TracaMailAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method TracaMailAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method TracaMailAdmin[]    findAll()
 * @method TracaMailAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TracaMailAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TracaMailAdmin::class);
    }

    public function add(TracaMailAdmin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TracaMailAdmin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TracaMailAdmin[] Returns an array of TracaMailAdmin objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TracaMailAdmin
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
