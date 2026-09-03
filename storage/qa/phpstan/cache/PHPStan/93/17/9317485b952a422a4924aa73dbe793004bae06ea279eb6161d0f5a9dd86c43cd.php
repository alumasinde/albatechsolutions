<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-openssl_encrypt
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'openssl_encrypt',
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
        'startLine' => 30,
        'endLine' => 30,
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
        'startLine' => 31,
        'endLine' => 31,
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
        'startLine' => 32,
        'endLine' => 32,
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
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 34,
            'startFilePos' => 1218,
            'endTokenPos' => 34,
            'endFilePos' => 1218,
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
        'startLine' => 33,
        'endLine' => 33,
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
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 43,
            'startFilePos' => 1242,
            'endTokenPos' => 43,
            'endFilePos' => 1243,
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
        'startLine' => 34,
        'endLine' => 34,
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
            'startLine' => 36,
            'endLine' => 36,
            'startTokenPos' => 61,
            'startFilePos' => 1345,
            'endTokenPos' => 61,
            'endFilePos' => 1348,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => true,
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
                  'startLine' => 35,
                  'endLine' => 35,
                  'startTokenPos' => 52,
                  'startFilePos' => 1321,
                  'endTokenPos' => 52,
                  'endFilePos' => 1325,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 35,
        'endLine' => 36,
        'startColumn' => 9,
        'endColumn' => 20,
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
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 80,
            'startFilePos' => 1456,
            'endTokenPos' => 80,
            'endFilePos' => 1457,
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
                  'startLine' => 37,
                  'endLine' => 37,
                  'startTokenPos' => 70,
                  'startFilePos' => 1426,
                  'endTokenPos' => 70,
                  'endFilePos' => 1430,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 37,
        'endLine' => 38,
        'startColumn' => 9,
        'endColumn' => 24,
        'parameterIndex' => 6,
        'isOptional' => true,
      ),
      'tag_length' => 
      array (
        'name' => 'tag_length',
        'default' => 
        array (
          'code' => '16',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 99,
            'startFilePos' => 1569,
            'endTokenPos' => 99,
            'endFilePos' => 1570,
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
                  'startTokenPos' => 89,
                  'startFilePos' => 1535,
                  'endTokenPos' => 89,
                  'endFilePos' => 1539,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 39,
        'endLine' => 40,
        'startColumn' => 9,
        'endColumn' => 28,
        'parameterIndex' => 7,
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
 * Encrypts data
 * @link https://php.net/manual/en/function.openssl-encrypt.php
 * @param string $data <p>
 * The data.
 * </p>
 * @param string $cipher_algo <p>
 * The cipher method. For a list of available cipher methods, use {@see openssl_get_cipher_methods()}.
 * </p>
 * @param string $passphrase <p>
 * The key.
 * </p>
 * @param int $options [optional] <p>
 * options is a bitwise disjunction of the flags OPENSSL_RAW_DATA and OPENSSL_ZERO_PADDING.
 * </p>
 * @param string $iv [optional] <p>
 * A non-NULL Initialization Vector.
 * </p>
 * @param string &$tag [optional] <p>The authentication tag passed by reference when using AEAD cipher mode (GCM or CCM).</p>
 * @param string $aad [optional] <p>Additional authentication data.</p>
 * @param int $tag_length [optional] <p>
 * The length of the authentication tag. Its value can be between 4 and 16 for GCM mode.
 * </p>
 * @return string|false the encrypted string on success or false on failure.
 */',
    'startLine' => 29,
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
        'name' => 'openssl_encrypt',
        'filename' => 'phpstorm-stubs:openssl/openssl.stub',
        'extensionName' => 'openssl',
        'aliasName' => NULL,
      ),
    ),
  ),
));