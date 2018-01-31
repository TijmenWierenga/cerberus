<?php

namespace TijmenWierenga\Tests\Cerberus\Repository\Scope;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use TijmenWierenga\Cerberus\Repository\Scope\InMemoryScopeRepository;
use TijmenWierenga\Cerberus\Scope;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@devmob.com>
 */
class InMemoryScopeRepositoryTest extends TestCase
{
    public function testItReturnsAScopeByItsIdentifier()
    {
        $scope = new Scope('users_get');
        $repository = new InMemoryScopeRepository(new ArrayCollection([$scope]));
        $result = $repository->getScopeEntityByIdentifier($scope->getIdentifier());

        $this->assertEquals($scope, $result);
    }
}
