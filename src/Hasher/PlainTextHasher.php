<?php

namespace Cerberus\Hasher;

class PlainTextHasher implements HasherInterface
{
    public function hash(string $value): string
    {
        return $value;
    }

    public function verify(string $value, string $hashedValue): bool
    {
        return $value === $hashedValue;
    }
}
