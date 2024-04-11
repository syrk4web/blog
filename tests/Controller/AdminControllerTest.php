<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Entity\User;

class AdminControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        // Handle redirection
        $client->followRedirects(true);
        $container = $client->getContainer();

        // Case 1 : login failed
        $this->checkFormRedirect($client, 'noneregister', 'P@ssw0rd', '/login');

        // Simulate login programmaticaly to avoid hash and conflict
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $admin = $entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);   
        $client->loginUser($admin);

        // Case 2 : login work = stay on dashboard (no redirection)
        $crawler = $client->request('GET', '/admin/dashboard');
        $this->assertStringContainsString('/admin/dashboard', $crawler->getUri());
       
    }

    public function checkFormRedirect(KernelBrowser $client, string $username, string $password, string $expectedRedirectPath): void
    {
        // Access login page
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        // Fill form
        $form = $crawler->selectButton('Sign in')->form();
        // Check that form is selected
        $this->assertNotNull($form);
        $form['username'] = $username;
        $form['password'] = $password;
        // Check form is filled
        $this->assertEquals($username, $form['username']->getValue());
        $this->assertEquals($password, $form['password']->getValue());
        // Submit form
        $client->submit($form);
        $crawler = $client->request('GET', '/admin/dashboard');
        // Check that crawler url matching expected
        $this->assertStringContainsString($expectedRedirectPath, $crawler->getUri());
    }
}
