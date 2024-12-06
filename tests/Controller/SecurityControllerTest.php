<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageRendersCorrectly()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[name="username"]');
        $this->assertSelectorExists('input[name="password"]');
        $this->assertSelectorExists('button[type="submit"]');
    }
    
    public function testRedirectIfAlreadyLoggedIn()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $manager = $container->get('doctrine')->getManager();
        
        $user = new User();
        $user->setPassword('<PASSWORD>');
        $user->setEmail('test@example.com');
        $user->setUsername('test');
        $manager->persist($user);
        $manager->flush();
        
        $client->loginUser($user);
        
        $client->request('GET', '/login');
        $this->assertResponseRedirects('/');
    }
    
    public function testLoginWithInvalidCredentialsShowsError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        
        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'invalid_user',
            'password' => 'invalid_pass',
        ]);
        
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        
        // Suivez la redirection
        $client->followRedirect();
        
        // Vérifiez que la réponse est correcte après la redirection
        $this->assertResponseIsSuccessful();
        
        // Assurez-vous qu'une erreur est affichée
        $this->assertSelectorExists('.alert.alert-danger'); // Assume there's a class 'error' for displaying errors
    }
}