<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-session_name
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'session_name',
    'parameters' => 
    array (
      'name' => 
      array (
        'name' => 'name',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 59,
            'startFilePos' => 1216,
            'endTokenPos' => 59,
            'endFilePos' => 1219,
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
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.0\' => \'null|string\']',
                'attributes' => 
                array (
                  'startLine' => 26,
                  'endLine' => 26,
                  'startTokenPos' => 35,
                  'startFilePos' => 1142,
                  'endTokenPos' => 41,
                  'endFilePos' => 1165,
                ),
              ),
              'default' => 
              array (
                'code' => '\'string\'',
                'attributes' => 
                array (
                  'startLine' => 26,
                  'endLine' => 26,
                  'startTokenPos' => 47,
                  'startFilePos' => 1177,
                  'endTokenPos' => 47,
                  'endFilePos' => 1184,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 26,
        'endLine' => 27,
        'startColumn' => 9,
        'endColumn' => 32,
        'parameterIndex' => 0,
        'isOptional' => true,
      ),
    ),
    'returnsReference' => false,
    'returnType' => 
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
              'name' => 'string',
              'isIdentifier' => true,
            ),
          ),
          1 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'false',
              'isIdentifier' => true,
            ),
          ),
        ),
      ),
    ),
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '[\'8.0\' => \'string|false\']',
            'attributes' => 
            array (
              'startLine' => 24,
              'endLine' => 24,
              'startTokenPos' => 11,
              'startFilePos' => 1006,
              'endTokenPos' => 17,
              'endFilePos' => 1030,
            ),
          ),
          'default' => 
          array (
            'code' => '\'string\'',
            'attributes' => 
            array (
              'startLine' => 24,
              'endLine' => 24,
              'startTokenPos' => 23,
              'startFilePos' => 1042,
              'endTokenPos' => 23,
              'endFilePos' => 1049,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * Get and/or set the current session name.<br/>
 * Before 7.2.0 checked cookie status and since 7.2.0 checks both cookie and session status to avoid PHP crash.
 * @link https://php.net/manual/en/function.session-name.php
 * @param string|null $name [optional] <p>
 * The session name references the name of the session, which is
 * used in cookies and URLs (e.g. PHPSESSID). It
 * should contain only alphanumeric characters; it should be short and
 * descriptive (i.e. for users with enabled cookie warnings).
 * If <i>name</i> is specified, the name of the current
 * session is changed to its value.
 * </p>
 * <p>
 * <p>
 * The session name can\'t consist of digits only, at least one letter
 * must be present. Otherwise a new session id is generated every time.
 * </p>
 * </p>
 * @return string|false the name of the current session.
 */',
    'startLine' => 24,
    'endLine' => 30,
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
        'name' => 'session_name',
        'filename' => 'phpstorm-stubs:session/session.stub',
        'extensionName' => 'session',
        'aliasName' => NULL,
      ),
    ),
  ),
));