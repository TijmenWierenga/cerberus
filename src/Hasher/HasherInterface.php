<?php
namespace Cerberus\Hasher;

interface HasherInterface
{
    public function hash(string $value): string;

    public function verify(string $value, string $hashedValue): bool;
}
