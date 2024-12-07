<?php

namespace App\Tests\Form\User;

use App\Entity\User;
use App\Form\User\UserPasswordType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserPasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => [
                'first' => 'password123',
                'second' => 'password123',
            ],
        ];
        
        $model = new User();
        $form = $this->factory->create(UserPasswordType::class, $model);
        
        // Soumet les données au formulaire
        $form->submit($formData);
        
        // L'entité User modifiée doit être équivalente à celle créée
        $expected = new User();
        $expected->setUsername('testuser');
        $expected->setEmail('test@example.com');
        $expected->setPassword('password123');
        
        // Assure que le formulaire est valide
        $this->assertTrue($form->isSynchronized());
        
        // Assure que les données du formulaire correspondent à ce qui est attendu
        $this->assertEquals($expected, $model);
        
        // Vérifie que les champs définis dans le formulaire correspondent
        $view = $form->createView();
        $children = $view->children;
        
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
    
    public function testSubmitInvalidPassword(): void
    {
        $formData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => [
                'first' => 'password123',
                'second' => 'differentPassword',
            ],
        ];
        
        $model = new User();
        $form = $this->factory->create(UserPasswordType::class, $model);
        
        // Soumet des données incorrectes
        $form->submit($formData);
        
        // Contrôle de la validité du formulaire
        $this->assertFalse($form->isValid());
        
        // Assure que la synchronisation est réussie même si les données sont invalides
        $this->assertTrue($form->isSynchronized());
    }
}
