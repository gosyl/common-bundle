<?php

namespace Gosyl\CommonBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testCheck()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login_check');
    }

    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
    }

    public function testLogout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/logout');
    }

}
