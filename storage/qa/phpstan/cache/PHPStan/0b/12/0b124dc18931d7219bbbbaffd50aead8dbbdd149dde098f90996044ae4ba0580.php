<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-session_set_cookie_params
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'session_set_cookie_params',
    'parameters' => 
    array (
      'lifetime_or_options' => 
      array (
        'name' => 'lifetime_or_options',
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
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 40,
        'endColumn' => 65,
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
        'name' => 'bool',
        'isIdentifier' => true,
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Set the session cookie parameters
 * @link https://php.net/manual/en/function.session-set-cookie-params.php
 * @param array $lifetime_or_options <p>
 * An associative array which may have any of the keys lifetime, path, domain,
 * secure, httponly and samesite. The values have the same meaning as described
 * for the parameters with the same name. The value of the samesite element
 * should be either Lax or Strict. If any of the allowed options are not given,
 * their default values are the same as the default values of the explicit
 * parameters. If the samesite element is omitted, no SameSite cookie attribute
 * is set.
 * </p>
 * @return bool returns true on success or false on failure.
 * @since 7.3
 */',
    'startLine' => 19,
    'endLine' => 21,
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
        'name' => 'session_set_cookie_params',
        'filename' => 'phpstorm-stubs:session/session.stub',
        'extensionName' => 'session',
        'aliasName' => NULL,
      ),
    ),
  ),
));