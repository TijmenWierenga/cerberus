<?php

namespace Cerberus\Hasher;

class ArgonPasswordHasher implements HasherInterface
{
    /**
     * @var array
     */
    private $options;

    public function __construct(int $cost = null)
    {
        $this->options = [
            'memory_cost' => $cost ?? PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS,
        ];
    }

    public function hash(string $value): string
    {
        return password_hash($value, PASSWORD_ARGON2I);
    }

    public function verify(string $value, string $hashedValue): bool
    {
        return password_verify($value, $hashedValue);
    }
}
