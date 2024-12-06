<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    
    public function testListActionAccessDeniedNotAuth(): void
    {
        
        $this->client->request('GET', '/admin/users');
        
        // Vérifie que l'accès est refusé sans le rôle ADMIN
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
    }
    
    public function testListActionAccessDeniedUser(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setPassword('<PASSWORD>');
        
        $manager = $this->client->getContainer()->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();
        
        $this->client->loginUser($user);
        
        $this->client->request('GET', '/admin/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
    
    public function testListActionAccessAuthorizeAdmin(): void
    {
        $this->connectAdmin();
        
        $this->client->request('GET', '/admin/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    private function connectAdmin(): void
    {
        $user = new User();
        $user->setUsername('testAdmin');
        $user->setEmail('testAdmin@example.com');
        $user->setPassword('<PASSWORD>');
        $user->setRoles(['ROLE_ADMIN']);
        
        $manager = $this->client->getContainer()->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();
        
        $this->client->loginUser($user);
    }
    
    public function testCreateActionSuccessfulByUser(): void
    {
        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form(); // Adaptez le bouton à votre formulaire
        $form['user_password[username]'] = 'Test User';
        $form['user_password[email]'] = 'test@example.com';
        $form['user_password[password][first]'] = '<PASSWORD>';
        $form['user_password[password][second]'] = '<PASSWORD>';
        
        // Soumettre le formulaire
        $this->client->submit($form);
        
        // Vérifiez la redirection vers 'app_login'
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        
        // Vérifiez la présence du message flash
        $this->assertSelectorTextContains('.alert-success', "L'utilisateur a bien été ajouté.");
    }
    
    public function testCreateActionSuccessfulByAdmin(): void
    {
        
        $this->connectAdmin();
        
        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user_role[username]'] = 'Test User';
        $form['user_role[email]'] = 'test2@example.com';
        $form['user_role[roles]'] = 'ROLE_USER';
        
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/users');
        
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', "L'utilisateur a bien été ajouté.");
    }
    
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
}