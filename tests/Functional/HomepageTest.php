<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace Tests\Functional;

class HomepageTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'Spindle' but not a hello world like greeting.
     */
    public function testGetHomepageWithoutName()
    {
        $response = $this->runApp('GET', '/');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('spindles', (string) $response->getBody());
        $this->assertNotContains('Hello', (string) $response->getBody());
    }

    /**
     * Test that the index route won't accept a post request.
     */
    public function testPostHomepageNotAllowed()
    {
        $response = $this->runApp('POST', '/', ['test']);

        $this->assertSame(405, $response->getStatusCode());
        $this->assertContains('Method not allowed', (string) $response->getBody());
    }
}
