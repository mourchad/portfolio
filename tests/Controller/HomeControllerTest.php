<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html', 'Portfolio');
    }

    public function testContactFormExists(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSelectorExists('form[name="contact"]');
        $this->assertSelectorExists('input[name="contact[name]"]');
        $this->assertSelectorExists('input[name="contact[email]"]');
        $this->assertSelectorExists('textarea[name="contact[content]"]');
    }
}
