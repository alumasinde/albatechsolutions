<?php declare(strict_types = 1);

// osfsl-C:/xampp/htdocs/albatechsolutions/vendor/composer/../php-di/invoker/src/InvokerInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Invoker\InvokerInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-3bad426241de52af48b6e18b8b56d2cc905fa3e8efd2c4fe7c4dccaad47238ea-8.2.12-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Invoker\\InvokerInterface',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/vendor/composer/../php-di/invoker/src/InvokerInterface.php',
      ),
    ),
    'namespace' => 'Invoker',
    'name' => 'Invoker\\InvokerInterface',
    'shortName' => 'InvokerInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Invoke a callable.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 25,
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
    ),
    'immediateMethods' => 
    array (
      'call' => 
      array (
        'name' => 'call',
        'parameters' => 
        array (
          'callable' => 
          array (
            'name' => 'callable',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 26,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 24,
                'endLine' => 24,
                'startTokenPos' => 54,
                'startFilePos' => 719,
                'endTokenPos' => 55,
                'endFilePos' => 720,
              ),
            ),
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
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 37,
            'endColumn' => 58,
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
 * Call the given function using the given parameters.
 *
 * @param callable|array|string $callable Function to call.
 * @param array $parameters Parameters to use.
 * @return mixed Result of the function.
 * @throws InvocationException Base exception class for all the sub-exceptions below.
 * @throws NotCallableException
 * @throws NotEnoughParametersException
 */',
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 60,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Invoker',
        'declaringClassName' => 'Invoker\\InvokerInterface',
        'implementingClassName' => 'Invoker\\InvokerInterface',
        'currentClassName' => 'Invoker\\InvokerInterface',
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