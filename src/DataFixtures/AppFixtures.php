<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class AppFixtures extends Fixture
{
    public function __construct(private readonly UserService $userService, private readonly TaskService $taskService)
    {
    }
    
    public function load(ObjectManager $manager): void
    {
        $users = [];
        
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@localhost.fr');
        $admin->setPassword('admin.1234');
        $admin->setRoles(['ROLE_ADMIN']);
        $this->userService->encoderPassword($admin);
        $this->userService->save($admin);
        
        $users[] = $admin;
        
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@localhost.fr');
        $user->setPassword('user.1234');
        $this->userService->save($user);
        $this->userService->encoderPassword($user);
        $users[] = $user;
        
        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setUsername('user' . $i);
            $user->setEmail('user' . $i . '@localhost.fr');
            $user->setPassword('user' . $i . '.1234');
            $this->userService->encoderPassword($user);
            $this->userService->save($user);
            $users[] = $user;
        }
        
        for ($i = 0; $i < 100; $i++) {
            $task = new Task();
            $task->setTitle('Task ' . $i);
            $task->setUser($users[rand(0, count($users) - 1)]);
            $task->setContent('Content of task ' . $i);
            
            $this->taskService->save($task);
        }
        
        
    }
}
