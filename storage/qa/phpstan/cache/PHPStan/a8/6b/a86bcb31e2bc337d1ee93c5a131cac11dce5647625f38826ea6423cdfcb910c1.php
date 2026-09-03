<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionFunction-date_default_timezone_set
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'name' => 'date_default_timezone_set',
    'parameters' => 
    array (
      'timezoneId' => 
      array (
        'name' => 'timezoneId',
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
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 40,
        'endColumn' => 57,
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
    ),
    'docComment' => '/**
 * Sets the default timezone used by all date/time functions in a script
 * @link https://php.net/manual/en/function.date-default-timezone-set.php
 * @param string $timezoneId <p>
 * The timezone identifier, like UTC or
 * Europe/Lisbon. The list of valid identifiers is
 * available in the .
 * </p>
 * @return bool This function returns false if the
 * timezone_identifier isn\'t valid, or true
 * otherwise.
 */',
    'startLine' => 16,
    'endLine' => 18,
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
        'name' => 'date_default_timezone_set',
        'filename' => 'phpstorm-stubs:date/date.stub',
        'extensionName' => 'date',
        'aliasName' => NULL,
      ),
    ),
  ),
));