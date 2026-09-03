<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-finfo_close
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'finfo_close',
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
                  'startLine' => 16,
                  'endLine' => 16,
                  'startTokenPos' => 48,
                  'startFilePos' => 620,
                  'endTokenPos' => 54,
                  'endFilePos' => 637,
                ),
              ),
              'default' => 
              array (
                'code' => '\'resource\'',
                'attributes' => 
                array (
                  'startLine' => 16,
                  'endLine' => 16,
                  'startTokenPos' => 60,
                  'startFilePos' => 649,
                  'endTokenPos' => 60,
                  'endFilePos' => 658,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 16,
        'endLine' => 17,
        'startColumn' => 9,
        'endColumn' => 20,
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
      0 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '[\'8.5\' => \'true\']',
            'attributes' => 
            array (
              'startLine' => 13,
              'endLine' => 13,
              'startTokenPos' => 11,
              'startFilePos' => 411,
              'endTokenPos' => 17,
              'endFilePos' => 427,
            ),
          ),
          'default' => 
          array (
            'code' => '\'bool\'',
            'attributes' => 
            array (
              'startLine' => 13,
              'endLine' => 13,
              'startTokenPos' => 23,
              'startFilePos' => 439,
              'endTokenPos' => 23,
              'endFilePos' => 444,
            ),
          ),
        ),
      ),
      1 => 
      array (
        'name' => 'JetBrains\\PhpStorm\\Deprecated',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '\'Deprecated: it has no effect\'',
            'attributes' => 
            array (
              'startLine' => 14,
              'endLine' => 14,
              'startTokenPos' => 30,
              'startFilePos' => 485,
              'endTokenPos' => 30,
              'endFilePos' => 514,
            ),
          ),
          'since' => 
          array (
            'code' => '\'8.5\'',
            'attributes' => 
            array (
              'startLine' => 14,
              'endLine' => 14,
              'startTokenPos' => 36,
              'startFilePos' => 524,
              'endTokenPos' => 36,
              'endFilePos' => 528,
            ),
          ),
        ),
      ),
    ),
    'docComment' => '/**
 * (PHP &gt;= 5.3.0, PECL fileinfo &gt;= 0.1.0)<br/>
 * Close fileinfo resource
 * @link https://php.net/manual/en/function.finfo-close.php
 * @param resource $finfo <p>
 * Fileinfo resource returned by finfo_open.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 */',
    'startLine' => 13,
    'endLine' => 20,
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
        'name' => 'finfo_close',
        'filename' => 'phpstorm-stubs:fileinfo/fileinfo.stub',
        'extensionName' => 'fileinfo',
        'aliasName' => NULL,
      ),
    ),
  ),
));