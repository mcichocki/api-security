<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUser()
    {
        $client = self::createClient();
        $client->request('POST', '/api/users', [
            'json' => [
                'email' => "mateusz@app.pl",
                'username' => "mateusz",
                'password' => '1234'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->logIn($client, 'mateusz@app.pl', '1234');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client, 'mateusz@app.pl', '1234');

        $client->request("PUT", '/api/users/'.$user->getId(), [
            'json' => [
                'username' => 'darek',
                'roles' => ['ROLE_ADMIN'] // będzie ignorowana
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => 'darek'
        ]);

        $em = $this->getEntityManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->find($user->getId());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

    }

    public function testGetUser()
    {
        $client = self::createClient();
        $user = $this->createUser('mateusz@app.pl', '1234');
        $this->createUserAndLogIn($client, 'authenticated@example.com', '1234');

        $user->setPhoneNumber('123-456-789');
        $em = $this->getEntityManager();
        $em->flush();

        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertJsonContains([
            'username' => 'mateusz'
        ]);

        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);

        // odświeżamy użytkownika i przypisujemy mu nową rolę
        $user = $em->getRepository(User::class)->find($user->getId());
        $user->setRoles(['ROLE_ADMIN']);
        $em->flush();
        $this->logIn($client, 'mateusz@app.pl', '1234');

        $client->request('GET', '/api/users/'.$user->getId());

        $this->assertJsonContains([
            'phoneNumber' => '123-456-789'
        ]);
    }
}