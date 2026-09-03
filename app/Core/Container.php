<?php

declare(strict_types=1);

namespace App\Core;

use DI\Container as PhpDiContainer;
use DI\ContainerBuilder;

/**
 * Container - thin static wrapper around PHP-DI.
 *
 * Kept intentionally minimal: this is not a framework, just a
 * resolver so controllers/services/repositories can declare their
 * dependencies in constructors instead of manual `new` chains.
 */
final class Container
{
    private static ?PhpDiContainer $instance = null;

    public static function boot(): void
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        self::$instance = $builder->build();
    }

    public static function resolve(string $class): object
    {
        if (self::$instance === null) {
            self::boot();
        }

        return self::$instance->get($class);
    }
}
