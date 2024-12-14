<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageRendersCorrectly(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[name="username"]');
        $this->assertSelectorExists('input[name="password"]');
        $this->assertSelectorExists('button[type="submit"]');
    }
    
    public function testRedirectIfAlreadyLoggedIn(): void
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
    
    public function testLoginWithInvalidCredentialsShowsError(): void
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
    
    
    public function testAuthenticationSuccessRedirectsHomepage(): void
    {
        $client = static::createClient();
        
        $container = $client->getContainer();
        $manager = $container->get('doctrine')->getManager();
        
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('testuser');
        $user->setPassword(password_hash('<PASSWORD>', PASSWORD_BCRYPT));
        $manager->persist($user);
        $manager->flush();
        
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'testuser',
            'password' => '<PASSWORD>',
        ]);
        
        $client->submit($form);
        
        $targetPath = '/tasks';
        
        $response = $client->getResponse();
        $redirectUrl = $response->headers->get('Location');
        
        if ($redirectUrl === $targetPath) {
            $this->assertResponseRedirects($targetPath);
        } else {
            $this->assertResponseRedirects('/');
        }
        
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
    
    public function testAuthenticationSuccessRedirectTargetPath(): void
    {
        $client = static::createClient();
        $session = $client->getContainer()->get('session.factory')->createSession();
        $container = $client->getContainer();
        $manager = $container->get('doctrine')->getManager();
        
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('testuser');
        $user->setPassword(password_hash('<PASSWORD>', PASSWORD_BCRYPT));
        $manager->persist($user);
        $manager->flush();
        
        $client->request('GET', '/tasks');
        $session->set('_security.main.target_path', '/tasks');
        $session->save();
        
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'testuser',
            'password' => '<PASSWORD>',
        ]);
        
        $client->submit($form);
        $this->assertResponseRedirects($session->get('_security.main.target_path'));
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
