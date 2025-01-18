<?php

namespace App\Repository;

use App\Entity\Motivation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Motivation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motivation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motivation[]    findAll()
 * @method Motivation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotivationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motivation::class);
    }

    /**
     * @param $date
     * @param $numSemaine
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMoyJour($date, $numSemaine)
    {
        return $this->createQueryBuilder('m')
            ->select('avg(m.vlMotivation) as value')
            ->where('m.numSemaine = :num')
            ->andWhere('DATE(m.dtSaisie) = :date')
            ->setParameter('num', $numSemaine)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $numSemaine
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMoySemaine($numSemaine)
    {
        return $this->createQueryBuilder('m')
            ->select('avg(m.vlMotivation) as value')
            ->where('m.numSemaine = :num')
            ->setParameter('num', $numSemaine)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $date
     * @param $numSemaine
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNbAbsents($date, $numSemaine)
    {
        return $this->createQueryBuilder('m')
            ->select('count(1) as value')
            ->where('m.numSemaine = :num')
            ->andWhere('DATE(m.dtSaisie) = :date')
            ->andWhere('m.absence = 1')
            ->setParameter('num', $numSemaine)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $date
     * @param $numSemaine
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTxParti($date, $numSemaine)
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.vlMotivation) as value')
            ->where('m.numSemaine = :num')
            ->andWhere('DATE(m.dtSaisie) = :date')
            ->setParameter('num', $numSemaine)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $date
     * @param $numSemaine
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCommentaires($date, $numSemaine)
    {
        return $this->createQueryBuilder('m')
            ->select('m.commentaire')
            ->where('m.numSemaine = :num')
            ->andWhere('DATE(m.dtSaisie) = :date')
            ->orderBy('m.commentaire', 'desc')
            ->setParameter('num', $numSemaine)
            ->setParameter('date', $date)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param $numSemaine
     * @param $vlMotiv
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNbMotiv($numSemaine, $vlMotiv)
    {
        return $this->createQueryBuilder('m')
            ->select('count(1) as value')
            ->where('m.numSemaine = :num')
            ->andWhere('m.vlMotivation = :vlMotiv')
            ->setParameter('num', $numSemaine)
            ->setParameter('vlMotiv', $vlMotiv)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $numSemaine
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHighestUser($numSemaine)
    {
        return $this->createQueryBuilder('m')
            ->select('identity(m.user) as value')
            ->where('m.numSemaine = :num')
            ->groupBy('value')
            ->orderBy('avg(m.vlMotivation)', 'desc')
            ->setParameter('num', $numSemaine)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $date
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHighestScore($date)
    {
        return $this->createQueryBuilder('m')
            ->select('MAX(m.vlMotivation) as value')
            ->where('DATE(m.dtSaisie) = :date')
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $date
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLowestScore($date)
    {
        return $this->createQueryBuilder('m')
            ->select('MIN(m.vlMotivation) as value')
            ->where('DATE(m.dtSaisie) = :date')
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
