<?php

namespace App\Tests\feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PhoneControllerTest extends WebTestCase
{
    public function testUnauthorizedUserAccessingEndpoints()
    {
        $client = static::createClient();

        $client->request('GET', '/api/user/phone');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRegisterUser()
    {
        $client = static::createClient();

        $data = array(
            'email' => 'tesgt@email.com',
            'password' => 'secret',
        );

        $client->request('POST', '/auth/register', $data);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
