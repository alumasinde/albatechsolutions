<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-set_exception_handler
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'set_exception_handler',
    'parameters' => 
    array (
      'callback' => 
      array (
        'name' => 'callback',
        'default' => NULL,
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
                  'name' => 'callable',
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
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 36,
        'endColumn' => 54,
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
 * Sets a user-defined exception handler function
 * @link https://php.net/manual/en/function.set-exception-handler.php
 * @param callable|null $callback <p>
 * Name of the function to be called when an uncaught exception occurs.
 * This function must be defined before calling
 * <b>set_exception_handler</b>. This handler function
 * needs to accept one parameter, which will be the exception object that
 * was thrown.
 * NULL may be passed instead, to reset this handler to its default state.
 * </p>
 * @return callable|null the name of the previously defined exception handler, or null on error. If
 * no previous handler was defined, null is also returned.
 */',
    'startLine' => 18,
    'endLine' => 20,
    'startColumn' => 5,
    'endColumn' => 5,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'set_exception_handler',
        'filename' => 'phpstorm-stubs:Core/Core.stub',
        'extensionName' => 'Core',
        'aliasName' => NULL,
      ),
    ),
  ),
));