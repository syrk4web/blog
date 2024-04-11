<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class RegisterControllerTest extends WebTestCase
{

    public function testRegister(): void
    {
        // Add 403 error on RegisterController and test it
        // + avoid delete users on same controller
        $client = static::createClient();
        // Allow to access dependency
        $container = $client->getContainer();

        // Case 1 : valid data but already user
        $this->checkRequest($client, '/register/admin/P@ssw0rd', 403);

        // Case 2 : invalid data but already user
        $this->checkRequest($client, '/register/test/test', 403);

        // Clear user database
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $users = $entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        // Case 3 : invalid data and no user
        $this->checkRequest($client, '/register/test/test', 403);
        
        // Case 4 : valid data and no user
        $this->checkRequest($client, '/register/admin/P@ssw0rd', 200);
    }

    public function checkRequest(KernelBrowser $client, string $path, int $expectedStatusCode): void
    {
        $client->request('GET', $path);
        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }
}
