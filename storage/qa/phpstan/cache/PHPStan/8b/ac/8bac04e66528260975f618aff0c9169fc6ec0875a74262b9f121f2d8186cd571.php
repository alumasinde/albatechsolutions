<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-session_start
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'session_start',
    'parameters' => 
    array (
      'options' => 
      array (
        'name' => 'options',
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 14,
            'startTokenPos' => 29,
            'startFilePos' => 876,
            'endTokenPos' => 30,
            'endFilePos' => 877,
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
            'isRepeated' => false,
            'arguments' => 
            array (
              'from' => 
              array (
                'code' => '\'7.0\'',
                'attributes' => 
                array (
                  'startLine' => 13,
                  'endLine' => 13,
                  'startTokenPos' => 19,
                  'startFilePos' => 843,
                  'endTokenPos' => 19,
                  'endFilePos' => 847,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 13,
        'endLine' => 14,
        'startColumn' => 9,
        'endColumn' => 27,
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
 * Initialize session data
 * @link https://php.net/manual/en/function.session-start.php
 * @param array $options [optional] <p>If provided, this is an associative array of options that will override the currently set session configuration directives. The keys should not include the session. prefix.
 * In addition to the normal set of configuration directives, a read_and_close option may also be provided. If set to TRUE, this will result in the session being closed immediately after being read, thereby avoiding unnecessary locking if the session data won\'t be changed.</p>
 * @return bool This function returns true if a session was successfully started,
 * otherwise false.
 */',
    'startLine' => 12,
    'endLine' => 17,
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
        'name' => 'session_start',
        'filename' => 'phpstorm-stubs:session/session.stub',
        'extensionName' => 'session',
        'aliasName' => NULL,
      ),
    ),
  ),
));