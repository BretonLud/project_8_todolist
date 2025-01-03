<?php

// src/Repository/TaskRepository.php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }
    
    public function save(Task $task): void
    {
        $manager = $this->getEntityManager();
        $manager->persist($task);
        $manager->flush();
    }
    
    public function remove(Task $task): void
    {
        $manager = $this->getEntityManager();
        $manager->remove($task);
        $manager->flush();
    }
    
    public function findTasksForUser(UserInterface $user, bool $accessAdmin): array
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'DESC');
        
        if ($accessAdmin) {
            $query->orWhere('t.user is null')
                ->orWhere('t.user != :user');
        }
        
        return $query->getQuery()->getResult();
    }
}
