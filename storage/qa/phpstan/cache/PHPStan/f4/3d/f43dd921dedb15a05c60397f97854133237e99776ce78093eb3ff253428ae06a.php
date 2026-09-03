<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-session_regenerate_id
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'session_regenerate_id',
    'parameters' => 
    array (
      'delete_old_session' => 
      array (
        'name' => 'delete_old_session',
        'default' => 
        array (
          'code' => '\\false',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 12,
            'startTokenPos' => 18,
            'startFilePos' => 423,
            'endTokenPos' => 18,
            'endFilePos' => 427,
          ),
        ),
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 36,
        'endColumn' => 67,
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
        'name' => 'bool',
        'isIdentifier' => true,
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Update the current session id with a newly generated one
 * @link https://php.net/manual/en/function.session-regenerate-id.php
 * @param bool $delete_old_session [optional] <p>
 * Whether to delete the old associated session file or not.
 * </p>
 * @return bool true on success or false on failure.
 */',
    'startLine' => 12,
    'endLine' => 14,
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
        'name' => 'session_regenerate_id',
        'filename' => 'phpstorm-stubs:session/session.stub',
        'extensionName' => 'session',
        'aliasName' => NULL,
      ),
    ),
  ),
));