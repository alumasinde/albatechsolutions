<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-session_get_cookie_params
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'session_get_cookie_params',
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
      0 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\ArrayShape',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '["lifetime" => "int", "path" => "string", "domain" => "string", "secure" => "bool", "httponly" => "bool", "samesite" => "string"]',
            'attributes' => 
            array (
              'startLine' => 20,
              'endLine' => 20,
              'startTokenPos' => 11,
              'startFilePos' => 658,
              'endTokenPos' => 52,
              'endFilePos' => 786,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Get the session cookie parameters
 * @link https://php.net/manual/en/function.session-get-cookie-params.php
 * @return array an array with the current session cookie information, the array
 * contains the following items:
 * "lifetime" - The
 * lifetime of the cookie in seconds.
 * "path" - The path where
 * information is stored.
 * "domain" - The domain
 * of the cookie.
 * "secure" - The cookie
 * should only be sent over secure connections.
 * "httponly" - The
 * cookie can only be accessed through the HTTP protocol.
 */',
    'startLine' => 20,
    'endLine' => 23,
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
        'name' => 'session_get_cookie_params',
        'filename' => 'phpstorm-stubs:session/session.stub',
        'extensionName' => 'session',
        'aliasName' => NULL,
      ),
    ),
  ),
));