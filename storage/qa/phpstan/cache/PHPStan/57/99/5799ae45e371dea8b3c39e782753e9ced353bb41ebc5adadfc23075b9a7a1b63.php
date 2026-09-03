<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\albatechsolutions\app\Core\SecurityHeaders.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Core\SecurityHeaders
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-550000710239241f07d5e309ee2166c040306da1eb9494d162abd32ea05e9baa',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Core\\SecurityHeaders',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/app/Core/SecurityHeaders.php',
      ),
    ),
    'namespace' => 'App\\Core',
    'name' => 'App\\Core\\SecurityHeaders',
    'shortName' => 'SecurityHeaders',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 7,
    'endLine' => 337,
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
      'all' => 
      array (
        'name' => 'all',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return all security headers for the current request.
 *
 * @return array<string, string>
 */',
        'startLine' => 14,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Core',
        'declaringClassName' => 'App\\Core\\SecurityHeaders',
        'implementingClassName' => 'App\\Core\\SecurityHeaders',
        'currentClassName' => 'App\\Core\\SecurityHeaders',
        'aliasName' => NULL,
      ),
      'contentSecurityPolicy' => 
      array (
        'name' => 'contentSecurityPolicy',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Build AlbaTech\'s Content Security Policy.
 *
 * @return string
 */',
        'startLine' => 90,
        'endLine' => 237,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Core',
        'declaringClassName' => 'App\\Core\\SecurityHeaders',
        'implementingClassName' => 'App\\Core\\SecurityHeaders',
        'currentClassName' => 'App\\Core\\SecurityHeaders',
        'aliasName' => NULL,
      ),
      'buildPolicy' => 
      array (
        'name' => 'buildPolicy',
        'parameters' => 
        array (
          'directives' => 
          array (
            'name' => 'directives',
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
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 41,
            'endColumn' => 57,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Convert directive arrays into a valid CSP header.
 *
 * @param array<string, array<int, string>> $directives
 */',
        'startLine' => 244,
        'endLine' => 253,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'App\\Core',
        'declaringClassName' => 'App\\Core\\SecurityHeaders',
        'implementingClassName' => 'App\\Core\\SecurityHeaders',
        'currentClassName' => 'App\\Core\\SecurityHeaders',
        'aliasName' => NULL,
      ),
      'isHttps' => 
      array (
        'name' => 'isHttps',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Detect HTTPS safely, including reverse proxies.
 */',
        'startLine' => 258,
        'endLine' => 336,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'App\\Core',
        'declaringClassName' => 'App\\Core\\SecurityHeaders',
        'implementingClassName' => 'App\\Core\\SecurityHeaders',
        'currentClassName' => 'App\\Core\\SecurityHeaders',
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