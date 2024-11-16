<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IsNotAuthenticated extends Voter
{
    public const ATTRIBUTE = 'IS_NOT_AUTHENTICATED';
    
    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::ATTRIBUTE === $attribute;
    }
    
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($token->getUser()) {
            return false;
        }
        
        return true;
    }
}