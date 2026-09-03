<?php declare(strict_types = 1);

// osfsl-C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/EventHint.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Sentry\EventHint
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-daf32806298970088b31063c0feb1fcbd8014b042394037d17d598a98146ffb1-8.2.12-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Sentry\\EventHint',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/EventHint.php',
      ),
    ),
    'namespace' => 'Sentry',
    'name' => 'Sentry\\EventHint',
    'shortName' => 'EventHint',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * This class represents hints on how to process an event.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 81,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'exception' => 
      array (
        'declaringClassName' => 'Sentry\\EventHint',
        'implementingClassName' => 'Sentry\\EventHint',
        'name' => 'exception',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The original exception to add to the event.
 *
 * @var \\Throwable|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'mechanism' => 
      array (
        'declaringClassName' => 'Sentry\\EventHint',
        'implementingClassName' => 'Sentry\\EventHint',
        'name' => 'mechanism',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * An object describing the mechanism of the original exception.
 *
 * @var ExceptionMechanism|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'stacktrace' => 
      array (
        'declaringClassName' => 'Sentry\\EventHint',
        'implementingClassName' => 'Sentry\\EventHint',
        'name' => 'stacktrace',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The stacktrace to set on the event.
 *
 * @var Stacktrace|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'extra' => 
      array (
        'declaringClassName' => 'Sentry\\EventHint',
        'implementingClassName' => 'Sentry\\EventHint',
        'name' => 'extra',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 54,
            'startFilePos' => 681,
            'endTokenPos' => 55,
            'endFilePos' => 682,
          ),
        ),
        'docComment' => '/**
 * Any extra data that might be needed to process the event.
 *
 * @var array<string, mixed>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'fromArray' => 
      array (
        'name' => 'fromArray',
        'parameters' => 
        array (
          'hintData' => 
          array (
            'name' => 'hintData',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 50,
            'endLine' => 50,
            'startColumn' => 38,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a EventHint instance from an array of values.
 *
 * @phpstan-param array{
 *     exception?: \\Throwable|null,
 *     mechanism?: ExceptionMechanism|null,
 *     stacktrace?: Stacktrace|null,
 *     extra?: array<string, mixed>
 * } $hintData
 */',
        'startLine' => 50,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\EventHint',
        'implementingClassName' => 'Sentry\\EventHint',
        'currentClassName' => 'Sentry\\EventHint',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));