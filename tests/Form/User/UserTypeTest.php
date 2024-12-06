<?php

namespace App\Tests\Form\User;

use App\Entity\User;
use App\Form\User\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ];
        
        $model = new User();
        $form = $this->factory->create(UserType::class, $model);
        
        // Soumet les données au formulaire
        $form->submit($formData);
        
        // L'entité User modifiée doit être équivalente à celle créée
        $expected = new User();
        $expected->setUsername('testuser');
        $expected->setEmail('test@example.com');
        
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
}
