<?php declare(strict_types = 1);

// odsl-C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/functions.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-Sentry\withScope
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-3bdda3598d2cb0ab79242d1348a2bfa6b02b8fd70a1b2be630ae0b1ea494eef6',
   'data' => 
  array (
    'name' => 'withScope',
    'parameters' => 
    array (
      'callback' => 
      array (
        'name' => 'callback',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'callable',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 219,
        'endLine' => 219,
        'startColumn' => 20,
        'endColumn' => 37,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
    ),
    'returnsReference' => false,
    'returnType' => NULL,
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Creates a new scope with and executes the given operation within. The scope
 * is automatically removed once the operation finishes or throws.
 *
 * @param callable $callback The callback to be executed
 *
 * @phpstan-template T
 *
 * @phpstan-param callable(Scope): T $callback
 *
 * @return mixed|void The callback\'s return value, upon successful execution
 *
 * @phpstan-return T
 */',
    'startLine' => 219,
    'endLine' => 222,
    'startColumn' => 1,
    'endColumn' => 1,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => 'Sentry',
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Sentry\\withScope',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/functions.php',
      ),
    ),
  ),
));