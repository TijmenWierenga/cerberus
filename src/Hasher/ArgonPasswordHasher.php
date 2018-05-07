<?php

namespace Cerberus\Hasher;

class ArgonPasswordHasher implements HasherInterface
{
    /**
     * @var array
     */
    private $options;

    public function __construct(int $cost = 2)
    {
        $this->options = [
            'memory_cost' => $cost,
            'time_cost'   => 4,
            'threads'     => 3,
        ];
    }

    public function hash(string $value): string
    {
        return password_hash($value, PASSWORD_ARGON2I, $this->options);
    }

    public function verify(string $value, string $hashedValue): bool
    {
        return password_verify($value, $hashedValue);
    }
}
