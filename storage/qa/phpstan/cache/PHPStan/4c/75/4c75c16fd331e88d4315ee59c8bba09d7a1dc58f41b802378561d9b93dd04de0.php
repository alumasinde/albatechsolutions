<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-session_status
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'session_status',
    'parameters' => 
    array (
    ),
    'returnsReference' => false,
    'returnType' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
      'data' => 
      array (
        'name' => 'int',
        'isIdentifier' => true,
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * (PHP 5 >= 5.4.0)<br>
 * Returns the current session status
 * @link https://php.net/manual/en/function.session-status.php
 * @return int <b>PHP_SESSION_DISABLED</b> if sessions are disabled.
 * <b>PHP_SESSION_NONE</b> if sessions are enabled, but none exists.
 * <b>PHP_SESSION_ACTIVE</b> if sessions are enabled, and one exists.
 * @since 5.4
 */',
    'startLine' => 13,
    'endLine' => 15,
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
        'name' => 'session_status',
        'filename' => 'phpstorm-stubs:session/session.stub',
        'extensionName' => 'session',
        'aliasName' => NULL,
      ),
    ),
  ),
));