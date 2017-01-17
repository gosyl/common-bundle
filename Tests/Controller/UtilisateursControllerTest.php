<?php

namespace Gosyl\CommonBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilisateursControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/utilisateurs');
    }

    public function testProfil()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/utilisateurs/profil');
    }

    public function testRegister()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
    }

}
