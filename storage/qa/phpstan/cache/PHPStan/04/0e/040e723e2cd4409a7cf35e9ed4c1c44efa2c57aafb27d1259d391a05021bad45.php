<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-openssl_decrypt
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'openssl_decrypt',
    'parameters' => 
    array (
      'data' => 
      array (
        'name' => 'data',
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
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 9,
        'endColumn' => 20,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'cipher_algo' => 
      array (
        'name' => 'cipher_algo',
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
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 9,
        'endColumn' => 27,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'passphrase' => 
      array (
        'name' => 'passphrase',
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
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 9,
        'endColumn' => 26,
        'parameterIndex' => 2,
        'isOptional' => false,
      ),
      'options' => 
      array (
        'name' => 'options',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 34,
            'startFilePos' => 1085,
            'endTokenPos' => 34,
            'endFilePos' => 1085,
          ),
        ),
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 9,
        'endColumn' => 24,
        'parameterIndex' => 3,
        'isOptional' => true,
      ),
      'iv' => 
      array (
        'name' => 'iv',
        'default' => 
        array (
          'code' => '""',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 43,
            'startFilePos' => 1109,
            'endTokenPos' => 43,
            'endFilePos' => 1110,
          ),
        ),
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
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 9,
        'endColumn' => 23,
        'parameterIndex' => 4,
        'isOptional' => true,
      ),
      'tag' => 
      array (
        'name' => 'tag',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 83,
            'startFilePos' => 1331,
            'endTokenPos' => 83,
            'endFilePos' => 1334,
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
                  'name' => 'string',
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
            'isRepeated' => false,
            'arguments' => 
            array (
              'from' => 
              array (
                'code' => '\'7.1\'',
                'attributes' => 
                array (
                  'startLine' => 36,
                  'endLine' => 36,
                  'startTokenPos' => 52,
                  'startFilePos' => 1188,
                  'endTokenPos' => 52,
                  'endFilePos' => 1192,
                ),
              ),
            ),
          ),
          1 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '[\'8.1\' => \'string|null\']',
                'attributes' => 
                array (
                  'startLine' => 37,
                  'endLine' => 37,
                  'startTokenPos' => 59,
                  'startFilePos' => 1258,
                  'endTokenPos' => 65,
                  'endFilePos' => 1281,
                ),
              ),
              'default' => 
              array (
                'code' => '\'string\'',
                'attributes' => 
                array (
                  'startLine' => 37,
                  'endLine' => 37,
                  'startTokenPos' => 71,
                  'startFilePos' => 1293,
                  'endTokenPos' => 71,
                  'endFilePos' => 1300,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 36,
        'endLine' => 38,
        'startColumn' => 9,
        'endColumn' => 31,
        'parameterIndex' => 5,
        'isOptional' => true,
      ),
      'aad' => 
      array (
        'name' => 'aad',
        'default' => 
        array (
          'code' => '""',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 102,
            'startFilePos' => 1442,
            'endTokenPos' => 102,
            'endFilePos' => 1443,
          ),
        ),
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
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
            'isRepeated' => false,
            'arguments' => 
            array (
              'from' => 
              array (
                'code' => '\'7.1\'',
                'attributes' => 
                array (
                  'startLine' => 39,
                  'endLine' => 39,
                  'startTokenPos' => 92,
                  'startFilePos' => 1412,
                  'endTokenPos' => 92,
                  'endFilePos' => 1416,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 39,
        'endLine' => 40,
        'startColumn' => 9,
        'endColumn' => 24,
        'parameterIndex' => 6,
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
    ),
    'docComment' => '/**
 * Decrypts data
 * @link https://php.net/manual/en/function.openssl-decrypt.php
 * @param string $data <p>
 * The data.
 * </p>
 * @param string $cipher_algo <p>
 * The cipher method.
 * </p>
 * @param string $passphrase <p>
 * The password.
 * </p>
 * @param int $options [optional] <p>
 * Setting to true will take a raw encoded string,
 * otherwise a base64 string is assumed for the
 * <i>data</i> parameter.
 * </p>
 * @param string $iv [optional] <p>
 * A non-NULL Initialization Vector.
 * </p>
 * @param string|null $tag <p>
 * The authentication tag in AEAD cipher mode. If it is incorrect, the authentication fails and the function returns <b>FALSE</b>.
 * </p>
 * @param string $aad [optional] <p>Additional authentication data.</p>
 * @return string|false The decrypted string on success or false on failure.
 */',
    'startLine' => 30,
    'endLine' => 43,
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
        'name' => 'openssl_decrypt',
        'filename' => 'phpstorm-stubs:openssl/openssl.stub',
        'extensionName' => 'openssl',
        'aliasName' => NULL,
      ),
    ),
  ),
));