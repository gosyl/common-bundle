<?php

namespace Gosyl\CommonBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AjaxControllerTest extends WebTestCase
{
    public function testBanutilisateur()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/banutilisateur');
    }

    public function testListerutilisateur()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/listerutilisateur');
    }

    public function testModifierutilisateur()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/modifierutilisateur');
    }

    public function testRestaurerutilisateur()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/restaurerutilisateur');
    }

    public function testSupprimerutilisateur()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/supprimerutilisateur');
    }

}
