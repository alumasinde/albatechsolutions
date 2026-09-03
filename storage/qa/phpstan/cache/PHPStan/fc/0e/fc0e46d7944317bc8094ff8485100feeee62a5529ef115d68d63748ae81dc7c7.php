<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-headers_sent
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'headers_sent',
    'parameters' => 
    array (
      'filename' => 
      array (
        'name' => 'filename',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 17,
            'startFilePos' => 668,
            'endTokenPos' => 17,
            'endFilePos' => 671,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => true,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 27,
        'endColumn' => 43,
        'parameterIndex' => 0,
        'isOptional' => true,
      ),
      'line' => 
      array (
        'name' => 'line',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 25,
            'startFilePos' => 683,
            'endTokenPos' => 25,
            'endFilePos' => 686,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => true,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 46,
        'endColumn' => 58,
        'parameterIndex' => 1,
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
 * Checks if or where headers have been sent
 * @link https://php.net/manual/en/function.headers-sent.php
 * @param string &$filename [optional] <p>
 * If the optional file and
 * line parameters are set,
 * headers_sent will put the PHP source file name
 * and line number where output started in the file
 * and line variables.
 * </p>
 * @param int &$line [optional] <p>
 * The line number where the output started.
 * </p>
 * @return bool headers_sent will return false if no HTTP headers
 * have already been sent or true otherwise.
 */',
    'startLine' => 20,
    'endLine' => 22,
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
        'name' => 'headers_sent',
        'filename' => 'phpstorm-stubs:standard/standard_4.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
  ),
));