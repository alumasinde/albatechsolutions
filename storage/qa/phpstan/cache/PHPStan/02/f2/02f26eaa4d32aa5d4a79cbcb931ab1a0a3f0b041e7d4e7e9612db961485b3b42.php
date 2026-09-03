<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-extract
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'extract',
    'parameters' => 
    array (
      'array' => 
      array (
        'name' => 'array',
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
        'byRef' => true,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 9,
        'endColumn' => 21,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'flags' => 
      array (
        'name' => 'flags',
        'default' => 
        array (
          'code' => '\\EXTR_OVERWRITE',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 58,
            'startFilePos' => 1496,
            'endTokenPos' => 58,
            'endFilePos' => 1509,
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
            'name' => 'JetBrains\\PhpStorm\\ExpectedValues',
            'isRepeated' => false,
            'arguments' => 
            array (
              'flags' => 
              array (
                'code' => '[\\EXTR_OVERWRITE, \\EXTR_SKIP, \\EXTR_PREFIX_SAME, \\EXTR_PREFIX_ALL, \\EXTR_PREFIX_INVALID, \\EXTR_IF_EXISTS, \\EXTR_PREFIX_IF_EXISTS, \\EXTR_REFS]',
                'attributes' => 
                array (
                  'startLine' => 32,
                  'endLine' => 32,
                  'startTokenPos' => 25,
                  'startFilePos' => 1339,
                  'endTokenPos' => 48,
                  'endFilePos' => 1471,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 32,
        'endLine' => 33,
        'startColumn' => 9,
        'endColumn' => 35,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'prefix' => 
      array (
        'name' => 'prefix',
        'default' => 
        array (
          'code' => '""',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 67,
            'startFilePos' => 1537,
            'endTokenPos' => 67,
            'endFilePos' => 1538,
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
        'endColumn' => 27,
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
        'name' => 'int',
        'isIdentifier' => true,
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Import variables into the current symbol table from an array
 * @link https://php.net/manual/en/function.extract.php
 * @param array &$array <p>
 * Note that prefix is only required if
 * extract_type is EXTR_PREFIX_SAME,
 * EXTR_PREFIX_ALL, EXTR_PREFIX_INVALID
 * or EXTR_PREFIX_IF_EXISTS. If
 * the prefixed result is not a valid variable name, it is not
 * imported into the symbol table. Prefixes are automatically separated from
 * the array key by an underscore character.
 * </p>
 * @param int $flags <p>
 * The way invalid/numeric keys and collisions are treated is determined
 * by the extract_type. It can be one of the
 * following values:
 * EXTR_OVERWRITE
 * If there is a collision, overwrite the existing variable.</p>
 * @param string $prefix <p>Only overwrite the variable if it already exists in the
 * current symbol table, otherwise do nothing. This is useful
 * for defining a list of valid variables and then extracting
 * only those variables you have defined out of
 * $_REQUEST, for example.</p>
 * @return int the number of variables successfully imported into the symbol
 * table.
 */',
    'startLine' => 30,
    'endLine' => 37,
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
        'name' => 'extract',
        'filename' => 'phpstorm-stubs:standard/standard_8.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));