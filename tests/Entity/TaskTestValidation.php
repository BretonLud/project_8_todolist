<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskTestValidation extends KernelTestCase
{
    public function testTitleNotBlankConstraint(): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        
        $task = new Task();
        $task->setTitle('');
        $task->setContent('Test content');
        $violations = $validator->validate($task);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Vous devez saisir un titre.', $violations[0]->getMessage());
    }
    
    public function testContentNotBlankConstraint(): void
    {
        $validator = self::getContainer()->get(ValidatorInterface::class);
        
        $task = new Task();
        $task->setTitle('Test title');
        $task->setContent('');
        $violations = $validator->validate($task);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Vous devez saisir du contenu.', $violations[0]->getMessage());
    }
}
