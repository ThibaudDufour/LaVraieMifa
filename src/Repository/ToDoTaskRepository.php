<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\ToDoTask;

class ToDoTaskRepository extends ServiceEntityRepository {
    
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ToDoTask::class);
    }

    // /**
    //  * @return ToDoTask[] Returns an array of ToDoTask objects
    //  */
    public function findNewToDoTask()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isDone= :val')
            ->setParameter('val', 0)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }
}