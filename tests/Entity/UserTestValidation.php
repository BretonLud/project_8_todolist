<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTestValidation extends KernelTestCase
{
    public function testUsernameNotBlankConstraint(): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        
        $user = new User();
        $user->setUsername('');
        $user->setPassword('<PASSWORD>');
        $user->setEmail('test@example.com');
        $violations = $validator->validate($user);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Vous devez saisir un nom d\'utilisateur.', $violations[0]->getMessage());
    }
    
    public function testPasswordNotBlankConstraint(): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        
        $user = new User();
        $user->setPassword('');
        $user->setUsername('test');
        $user->setEmail('test@example.com');
        $violations = $validator->validate($user);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Vous devez saisir un mot de passe.', $violations[0]->getMessage());
    }
    
    public function testEmailNotBlankConstraint(): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        
        $user = new User();
        $user->setEmail('');
        $user->setUsername('test');
        $user->setPassword('<PASSWORD>');
        $violations = $validator->validate($user);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Vous devez saisir une adresse email.', $violations[0]->getMessage());
    }
    
    public function testEmailValidConstraint(): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        
        $user = new User();
        $user->setEmail('test');
        $user->setUsername('test');
        $user->setPassword('<PASSWORD>');
        $violations = $validator->validate($user);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Le format de l\'adresse n\'est pas correcte.', $violations[0]->getMessage());
    }
    
    public function testEmailUniqueConstraint(): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('test');
        $user->setPassword('<PASSWORD>');
        $manager = self::getContainer()->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();
        
        $user2 = new User();
        $user2->setEmail('test@example.com');
        $user2->setUsername('test2');
        $user2->setPassword('<PASSWORD>');
        $violations = $validator->validate($user2);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Il existe déjà une personne avec cet email.", $violations[0]->getMessage());
    }
    
    public function testUsernameUniqueConstraint(): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('test');
        $user->setPassword('<PASSWORD>');
        $manager = self::getContainer()->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();
        
        $user2 = new User();
        $user2->setEmail('test2@example.com');
        $user2->setUsername('test');
        $user2->setPassword('<PASSWORD>');
        
        $violations = $validator->validate($user2);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Il existe déjà une personne avec cet username.", $violations[0]->getMessage());
    }
}
