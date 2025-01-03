<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskAccess;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TaskAccessTest extends TestCase
{
    private Security $security;
    private TaskAccess $voter;
    
    /**
     * @throws Exception
     */
    public function testVoteWithUnsupportedAttribute(): void
    {
        $task = $this->createMock(Task::class);
        $token = $this->createMock(TokenInterface::class);
        
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($token, $task, ['NOT_SUPPORTED']));
    }
    
    /**
     * @throws Exception
     */
    public function testVoteWithUnsupportedSubject(): void
    {
        $stdObject = new \stdClass();
        $token = $this->createMock(TokenInterface::class);
        
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($token, $stdObject, [TaskAccess::TASK_ACCESS]));
    }
    
    /**
     * @throws Exception
     */
    public function testVoteOnAttributeAsAdmin(): void
    {
        $task = $this->createMock(Task::class);
        $task->method('getUser')->willReturn(null);
        
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($this->createMock(User::class));
        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $this->voter->vote($token, $task, [TaskAccess::TASK_ACCESS]));
    }
    
    /**
     * @throws Exception
     */
    public function testVoteOnAttributeAsOwner(): void
    {
        $user = $this->createMock(User::class);
        $task = $this->createMock(Task::class);
        $task->method('getUser')->willReturn($user);
        
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $this->voter->vote($token, $task, [TaskAccess::TASK_ACCESS]));
    }
    
    /**
     * @throws Exception
     */
    public function testVoteOnAttributeAsNonOwner(): void
    {
        $taskUser = $this->createMock(User::class);
        $anotherUser = $this->createMock(User::class);
        
        $task = $this->createMock(Task::class);
        $task->method('getUser')->willReturn($taskUser);
        
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($anotherUser);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, $task, [TaskAccess::TASK_ACCESS]));
    }
    
    /**
     * @throws Exception
     */
    public function testVoteOnAttributeAsUnauthenticated(): void
    {
        $task = $this->createMock(Task::class);
        
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, $task, [TaskAccess::TASK_ACCESS]));
    }
    
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->voter = new TaskAccess($this->security);
    }
}
