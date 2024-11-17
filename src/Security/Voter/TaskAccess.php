<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskAccess extends Voter
{
    public const TASK_ACCESS = 'TASK_ACCESS';
    
    public function __construct(
        private readonly Security $security
    )
    {
    }
    
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::TASK_ACCESS && $subject instanceof Task;
    }
    
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }
        
        if (!$subject->getUser() && $this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        
        if ($subject->getUser() === $user) {
            return true;
        }
        
        return false;
    }
}