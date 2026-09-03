<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-finfo_open
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'finfo_open',
    'parameters' => 
    array (
      'flags' => 
      array (
        'name' => 'flags',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 37,
            'startFilePos' => 908,
            'endTokenPos' => 37,
            'endFilePos' => 908,
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
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 25,
        'endColumn' => 38,
        'parameterIndex' => 0,
        'isOptional' => true,
      ),
      'magic_database' => 
      array (
        'name' => 'magic_database',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 47,
            'startFilePos' => 937,
            'endTokenPos' => 47,
            'endFilePos' => 940,
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
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 41,
        'endColumn' => 70,
        'parameterIndex' => 1,
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
              'name' => 'finfo',
              'isIdentifier' => false,
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
            'code' => '[\'8.1\' => \'finfo|false\']',
            'attributes' => 
            array (
              'startLine' => 22,
              'endLine' => 22,
              'startTokenPos' => 11,
              'startFilePos' => 817,
              'endTokenPos' => 17,
              'endFilePos' => 840,
            ),
          ),
          'default' => 
          array (
            'code' => '\'resource|false\'',
            'attributes' => 
            array (
              'startLine' => 22,
              'endLine' => 22,
              'startTokenPos' => 23,
              'startFilePos' => 852,
              'endTokenPos' => 23,
              'endFilePos' => 867,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * (PHP &gt;= 5.3.0, PECL fileinfo &gt;= 0.1.0)<br/>
 * Create a new fileinfo resource
 * @link https://php.net/manual/en/function.finfo-open.php
 * @param int $flags <p>
 * One or disjunction of more Fileinfo
 * constants.
 * </p>
 * @param string|null $magic_database [optional] <p>
 * Name of a magic database file, usually something like
 * /path/to/magic.mime. If not specified,
 * the MAGIC environment variable is used. If this variable
 * is not set either, /usr/share/misc/magic is used by default.
 * A .mime and/or .mgc suffix is added if
 * needed.
 * </p>
 * @return resource|false a magic database resource on success or <b>FALSE</b> on failure.
 */',
    'startLine' => 22,
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
        'name' => 'finfo_open',
        'filename' => 'phpstorm-stubs:fileinfo/fileinfo.stub',
        'extensionName' => 'fileinfo',
        'aliasName' => NULL,
      ),
    ),
  ),
));