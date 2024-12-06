<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    
    public function testListAction(): void
    {
        $this->client->request('GET', '/tasks');;
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.alert.alert-warning', "Il n'y a pas encore de tâche enregistrée.");
    }
    
    public function testCreateAction(): void
    {
        
        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Nouvelle Tâche',
            'task[content]' => 'Contenu de la nouvelle tâche',
        ]);
        
        $this->client->submit($form);
        
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        
        $this->assertSelectorTextContains('div.alert-success', 'La tâche a été bien été ajoutée.');
    }
    
    public function testEditAction(): void
    {
        
        $manager = $this->client->getContainer()->get('doctrine')->getManager();
        
        $task = new Task();
        $task->setTitle('Nouvelle Tâche');
        $task->setContent("Contenu de la nouvelle tâche");
        $task->setUser($this->client->getContainer()->get('security.token_storage')->getToken()->getUser());
        $manager->persist($task);
        $manager->flush();
        $taskId = $task->getId();
        
        $crawler = $this->client->request('GET', "/tasks/$taskId/edit");
        
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Tâche modifiée',
            'task[content]' => 'Contenu modifié de la tâche',
        ]);
        
        $this->client->submit($form);
        
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        
        $this->assertSelectorTextContains('div.alert-success', 'La tâche a bien été modifiée.');
    }
    
    public function testToggleTaskActionIsDone(): void
    {
        $manager = $this->client->getContainer()->get('doctrine')->getManager();
        $task = new Task();
        $task->setTitle('Nouvelle Tâche');
        $task->setContent("Contenu de la nouvelle tâche");
        
        $user = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser();
        $this->assertInstanceOf(User::class, $user);
        $task->setUser($user);
        
        $manager->persist($task);
        $manager->flush();
        
        $crawler = $this->client->request('GET', "/tasks");
        $form = $crawler->selectButton('Marquer comme faite')->form();
        
        $this->client->submit($form);
        
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'Superbe ! La tâche Nouvelle Tâche a bien été marquée comme faite.');
    }
    
    public function testToggleTaskActionIsNotDone(): void
    {
        $manager = $this->client->getContainer()->get('doctrine')->getManager();
        $task = new Task();
        $task->setTitle('Nouvelle Tâche');
        $task->setContent("Contenu de la nouvelle tâche");
        $task->toggle(true);
        
        $user = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser();
        $this->assertInstanceOf(User::class, $user);
        $task->setUser($user);
        
        $manager->persist($task);
        $manager->flush();
        
        $crawler = $this->client->request('GET', "/tasks");
        $form = $crawler->selectButton('Marquer non terminée')->form();
        
        $this->client->submit($form);
        
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'Superbe ! La tâche Nouvelle Tâche a bien été marquée comme non faite.');
    }
    
    public function testDeleteTaskAction(): void
    {
        $manager = $this->client->getContainer()->get('doctrine')->getManager();
        
        $task = new Task();
        $task->setTitle('Nouvelle Tâche');
        $task->setContent("Contenu de la nouvelle tâche");
        $task->setUser($this->client->getContainer()->get('security.token_storage')->getToken()->getUser());
        $manager->persist($task);
        $manager->flush();
        
        $crawler = $this->client->request('GET', "/tasks");
        $form = $crawler->selectButton('Supprimer')->form();
        $this->client->submit($form);
        
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'La tâche a bien été supprimée.');
    }
    
    protected function setUp(): void
    {
        $this->client = static::createClient();
        
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setPassword('<PASSWORD>');
        
        $manager = $this->client->getContainer()->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();
        
        $this->client->loginUser($user);
    }
}
