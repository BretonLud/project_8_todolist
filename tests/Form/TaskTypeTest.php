<?php

namespace App\Tests\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'Test Title',
            'content' => 'Test content',
        ];
        
        $model = new Task();
        $form = $this->factory->create(TaskType::class, $model);
        
        // Simule la soumission du formulaire
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        
        // Vérifie que le modèle a été rempli correctement
        $this->assertSame($formData['title'], $model->getTitle());
        $this->assertSame($formData['content'], $model->getContent());
        
        // Vérifie la structure du formulaire
        $view = $form->createView();
        $children = $view->children;
        
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
