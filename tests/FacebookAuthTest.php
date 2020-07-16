<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FacebookAuthTest extends WebTestCase
{
    public function testSomething()
    {
        $client = static::createClient(array(),
            array(
                'HTTP_HOST' => 'localhost',
            )
        );
        $client->followRedirects(false);
        $crawler = $client->request('GET', '/auth');
        dump($client->getRequest()->getUri());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Sign in');

    //     $crawler = $client->request('GET', '/auth/init/facebook');
    //     dump($client->getRequest()->getUri());

    //     $link = $crawler->filter('a');

    //     $crawler = $client->request('GET', $link->attr('href'));
    //     dump($client->getResponse()->isRedirect());
    //     dump($client->getRequest()->getUri());
    //     $crawler = $client->request('GET', $crawler->getUri());
    //     dump($crawler->getUri());
    //     dump($crawler->text());

    //     // $form = $crawler->filter('login')->form();

    //     // $form['email']->setValue('open_vwxoxnp_user@tfbnw.net');
    //     // $form['pass']->setValue('nNH@VEvvHj!B6h4');
    //     // $client->submit($form);


    //     // fb auth

    //     #$form = $crawler->selectButton('__CONFIRM__')->form();
    //     #$client->submit($form);
    }
}
