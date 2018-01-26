<?php

namespace TijmenWierenga\Tests\Cerberus\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use TijmenWierenga\Cerberus\Client;
use TijmenWierenga\Cerberus\Repository\Client\InMemoryClientRepository;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryClientRepositoryTest extends TestCase
{
    public function testItReturnsAClientById()
    {
        $client = Client::new(Uuid::uuid4(), 'tijmen', 'http://www.suchapp.com/callback');
        $repo = new InMemoryClientRepository(new ArrayCollection([$client]));

        $result = $repo->getClientEntity($client->getIdentifier(), 'client_credentials');

        $this->assertEquals($client, $result);
    }
}
