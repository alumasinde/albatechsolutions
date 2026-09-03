<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-password_hash
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'password_hash',
    'parameters' => 
    array (
      'password' => 
      array (
        'name' => 'password',
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
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 28,
        'endColumn' => 43,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'algo' => 
      array (
        'name' => 'algo',
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
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'int',
                  'isIdentifier' => true,
                ),
              ),
              2 => 
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
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 46,
        'endColumn' => 66,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'options' => 
      array (
        'name' => 'options',
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 58,
            'startFilePos' => 1353,
            'endTokenPos' => 59,
            'endFilePos' => 1354,
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
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 69,
        'endColumn' => 87,
        'parameterIndex' => 2,
        'isOptional' => true,
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
      0 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '["8.0" => "string"]',
            'attributes' => 
            array (
              'startLine' => 21,
              'endLine' => 21,
              'startTokenPos' => 11,
              'startFilePos' => 1197,
              'endTokenPos' => 17,
              'endFilePos' => 1215,
            ),
          ),
          'default' => 
          array (
            'code' => '"string|false|null"',
            'attributes' => 
            array (
              'startLine' => 21,
              'endLine' => 21,
              'startTokenPos' => 23,
              'startFilePos' => 1227,
              'endTokenPos' => 23,
              'endFilePos' => 1245,
            ),
          ),
        ),
      ),
      1 => 
      array (
        'name' => 'Pure',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '\\true',
            'attributes' => 
            array (
              'startLine' => 22,
              'endLine' => 22,
              'startTokenPos' => 30,
              'startFilePos' => 1261,
              'endTokenPos' => 30,
              'endFilePos' => 1264,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * (PHP 5 &gt;= 5.5.0, PHP 5)<br/>
 *
 * Creates a password hash.
 * @link https://secure.php.net/manual/en/function.password-hash.php
 * @param string $password The user\'s password.
 * @param string|int|null $algo A <a href="https://secure.php.net/manual/en/password.constants.php" class="link">password algorithm constant</a>  denoting the algorithm to use when hashing the password.
 * @param array $options [optional] <p> An associative array containing options. See the <a href="https://secure.php.net/manual/en/password.constants.php" class="link">password algorithm constants</a> for documentation on the supported options for each algorithm.</p>
 * If omitted, a random salt will be created and the default cost will be used.
 * <b>Warning</b>
 * <p>
 * The salt option has been deprecated as of PHP 7.0.0. It is now
 * preferred to simply use the salt that is generated by default.
 * </p>
 * @return string|false|null Returns the hashed password, or FALSE on failure (PRIOR PHP 8.0), or null if the algorithm is invalid
 * @since 5.5
 */',
    'startLine' => 21,
    'endLine' => 25,
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
        'name' => 'password_hash',
        'filename' => 'phpstorm-stubs:standard/password.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));