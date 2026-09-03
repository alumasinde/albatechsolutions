<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\albatechsolutions\app\Core\Helpers\functions.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-asset
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-7173fd7fd727489cc5ccfacbe45272a3fceaa39f29427bf152c3f8b1ffec7ddc',
   'data' => 
  array (
    'name' => 'asset',
    'parameters' => 
    array (
      'path' => 
      array (
        'name' => 'path',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 20,
        'endColumn' => 31,
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
 * Build a same-origin asset URL with automatic cache busting.
 *
 * Assets are deliberately root-relative instead of using APP_URL.
 * This prevents www/non-www mismatches and keeps CSP \'self\'
 * working correctly.
 *
 * Example:
 *
 * /assets/css/v2/production.css?v=1787398338
 */',
    'startLine' => 97,
    'endLine' => 156,
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
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'asset',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/app/Core/Helpers/functions.php',
      ),
    ),
  ),
));