<?php declare(strict_types = 1);

// osfsl-C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/SentrySdk.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Sentry\SentrySdk
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-fa63662ad8ce961b7bc6c25780aa721f0af66b15fbb77ec3f750497878451d23-8.2.12-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Sentry\\SentrySdk',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/SentrySdk.php',
      ),
    ),
    'namespace' => 'Sentry',
    'name' => 'Sentry\\SentrySdk',
    'shortName' => 'SentrySdk',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * This class is the main entry point for all the most common SDK features.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 167,
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
      'currentHub' => 
      array (
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'name' => 'currentHub',
        'modifiers' => 20,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var HubInterface|null The baseline hub
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'runtimeContextManager' => 
      array (
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'name' => 'runtimeContextManager',
        'modifiers' => 20,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var RuntimeContextManager|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 42,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Constructor.
 */',
        'startLine' => 34,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'init' => 
      array (
        'name' => 'init',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Sentry\\State\\HubInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Initializes the SDK by creating a new hub instance each time this method
 * gets called.
 */',
        'startLine' => 42,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'getCurrentHub' => 
      array (
        'name' => 'getCurrentHub',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Sentry\\State\\HubInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the current hub. If it\'s not initialized then creates a new instance
 * and sets it as current hub.
 */',
        'startLine' => 54,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'setCurrentHub' => 
      array (
        'name' => 'setCurrentHub',
        'parameters' => 
        array (
          'hub' => 
          array (
            'name' => 'hub',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Sentry\\State\\HubInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 68,
            'endLine' => 68,
            'startColumn' => 42,
            'endColumn' => 58,
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
            'name' => 'Sentry\\State\\HubInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sets the current hub.
 *
 * If called while an explicit runtime context is active, the hub update is
 * scoped to that active context only. Otherwise, it updates the baseline
 * hub used by the global fallback context and future contexts.
 *
 * @param HubInterface $hub The hub to set
 */',
        'startLine' => 68,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'startContext' => 
      array (
        'name' => 'startContext',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 79,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'endContext' => 
      array (
        'name' => 'endContext',
        'parameters' => 
        array (
          'timeout' => 
          array (
            'name' => 'timeout',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 84,
                'endLine' => 84,
                'startTokenPos' => 282,
                'startFilePos' => 2087,
                'endTokenPos' => 282,
                'endFilePos' => 2090,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 39,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 84,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'withContext' => 
      array (
        'name' => 'withContext',
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
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 40,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'timeout' => 
          array (
            'name' => 'timeout',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 105,
                'endLine' => 105,
                'startTokenPos' => 326,
                'startFilePos' => 2678,
                'endTokenPos' => 326,
                'endFilePos' => 2681,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 60,
            'endColumn' => 79,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Executes the given callback within an isolated context.
 *
 * If a context is already active for the current execution key, this method
 * reuses it and only executes the callback.
 *
 * @param callable $callback The callback to execute
 *
 * @phpstan-template T
 *
 * @phpstan-param callable(): T $callback
 *
 * @return mixed
 *
 * @phpstan-return T
 */',
        'startLine' => 105,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'getCurrentRuntimeContext' => 
      array (
        'name' => 'getCurrentRuntimeContext',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Sentry\\State\\RuntimeContext',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the current runtime-local context.
 *
 * @internal
 */',
        'startLine' => 128,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'flush' => 
      array (
        'name' => 'flush',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Flushes all buffered telemetry data.
 *
 * This is a convenience facade that forwards the flush operation to all
 * internally managed components.
 *
 * Calling this method is equivalent to invoking `flush()` on each component
 * individually. It does not change flushing behavior, improve performance,
 * or reduce the number of network requests.
 */',
        'startLine' => 143,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
        'aliasName' => NULL,
      ),
      'getRuntimeContextManager' => 
      array (
        'name' => 'getRuntimeContextManager',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Sentry\\State\\RuntimeContextManager',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 155,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\SentrySdk',
        'implementingClassName' => 'Sentry\\SentrySdk',
        'currentClassName' => 'Sentry\\SentrySdk',
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