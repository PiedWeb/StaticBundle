<?php

namespace PiedWeb\StaticBundle\Tests\Controller;

use PiedWeb\StaticBundle\Tests\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class StaticControllerTest extends WebTestCase
{
    public function testStaticGenerator()
    {
        //self::bootKernel();
        //$container = self::$kernel->getContainer();
        //$container = self::$container;

        $kernel = new AppKernel();

        $client = new Client($kernel);
        $client->request('GET', '/');
        //var_dump($client->getResponse()->getContent());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    private function testStatic()
    {
        $kernel = new AppKernel();

        $container = self::$kernel->getContainer();

        $container = self::$container;
        $static = self::$container->get('piedweb.static')->dump();
    }
}
