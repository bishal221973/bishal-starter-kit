<?php

namespace App\Services;

class ConfigService
{
    public function hello(string $name): string
    {
        return "Hello, {$name}!";
    }

    public function add(int $a, int $b): int
    {
        return $a + $b;
    }
}