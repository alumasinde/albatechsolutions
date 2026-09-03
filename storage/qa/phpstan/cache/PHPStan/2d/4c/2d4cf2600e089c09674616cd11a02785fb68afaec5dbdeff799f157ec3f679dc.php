<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-finfo_file
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'finfo_file',
    'parameters' => 
    array (
      'finfo' => 
      array (
        'name' => 'finfo',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'finfo',
            'isIdentifier' => false,
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
                'code' => '[\'8.1\' => \'finfo\']',
                'attributes' => 
                array (
                  'startLine' => 25,
                  'endLine' => 25,
                  'startTokenPos' => 16,
                  'startFilePos' => 814,
                  'endTokenPos' => 22,
                  'endFilePos' => 831,
                ),
              ),
              'default' => 
              array (
                'code' => '\'resource\'',
                'attributes' => 
                array (
                  'startLine' => 25,
                  'endLine' => 25,
                  'startTokenPos' => 28,
                  'startFilePos' => 843,
                  'endTokenPos' => 28,
                  'endFilePos' => 852,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 25,
        'endLine' => 26,
        'startColumn' => 9,
        'endColumn' => 20,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'filename' => 
      array (
        'name' => 'filename',
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
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 9,
        'endColumn' => 24,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'flags' => 
      array (
        'name' => 'flags',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 48,
            'startFilePos' => 925,
            'endTokenPos' => 48,
            'endFilePos' => 925,
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
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 9,
        'endColumn' => 22,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
      'context' => 
      array (
        'name' => 'context',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 55,
            'startFilePos' => 947,
            'endTokenPos' => 55,
            'endFilePos' => 950,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 9,
        'endColumn' => 23,
        'parameterIndex' => 3,
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
 * (PHP &gt;= 5.3.0, PECL fileinfo &gt;= 0.1.0)<br/>
 * Return information about a file
 * @link https://php.net/manual/en/function.finfo-file.php
 * @param resource $finfo <p>
 * Fileinfo resource returned by finfo_open.
 * </p>
 * @param string $filename <p>
 * Name of a file to be checked.
 * </p>
 * @param int $flags <p>
 * One or disjunction of more Fileinfo
 * constants.
 * </p>
 * @param resource $context [optional] <p>
 * For a description of contexts, refer to .
 * </p>
 * @return string|false a textual description of the contents of the
 * <i>filename</i> argument, or <b>FALSE</b> if an error occurred.
 */',
    'startLine' => 24,
    'endLine' => 32,
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
        'name' => 'finfo_file',
        'filename' => 'phpstorm-stubs:fileinfo/fileinfo.stub',
        'extensionName' => 'fileinfo',
        'aliasName' => NULL,
      ),
    ),
  ),
));