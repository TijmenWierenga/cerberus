<?php

namespace Cerberus\Tests\Functional\Hasher;

use Cerberus\Hasher\ArgonPasswordHasher;
use PHPUnit\Framework\TestCase;

class ArgonPasswordHasherTest extends TestCase
{
    /**
     * @var ArgonPasswordHasher
     */
    private $hasher;

    public function setUp()
    {
        $this->hasher = new ArgonPasswordHasher();
    }

    public function testItHashesAPassword()
    {
        $hash = $this->hasher->hash('a-password');

        $this->assertNotEquals('a-password', $hash);

        return $hash;
    }

    /**
     * @param string $hash
     * @depends testItHashesAPassword
     */
    public function testItVerifiesAPassword(string $hash)
    {
        $this->assertTrue($this->hasher->verify('a-password', $hash));
    }
}
