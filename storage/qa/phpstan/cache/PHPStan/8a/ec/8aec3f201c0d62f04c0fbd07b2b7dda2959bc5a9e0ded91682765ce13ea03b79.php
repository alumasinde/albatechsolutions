<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionConstant-PASSWORD_BCRYPT
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-dev-master@709e512-8.2.12',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'PASSWORD_BCRYPT',
        'filename' => 'phpstorm-stubs:standard/password.stub',
        'extensionName' => 'standard',
        'aliasName' => NULL,
      ),
    ),
    'name' => 'PASSWORD_BCRYPT',
    'shortName' => 'PASSWORD_BCRYPT',
    'value' => 
    array (
      'code' => '\'2y\'',
      'attributes' => 
      array (
        'startLine' => 25,
        'endLine' => 25,
        'startTokenPos' => 9,
        'startFilePos' => 694,
        'endTokenPos' => 9,
        'endFilePos' => 697,
      ),
    ),
    'docComment' => '/**
 * <p>
 * The default algorithm to use for hashing if no algorithm is provided.
 * This may change in newer PHP releases when newer, stronger hashing
 * algorithms are supported.
 * </p>
 * <p>
 * It is worth noting that over time this constant can (and likely will)
 * change. Therefore you should be aware that the length of the resulting
 * hash can change. Therefore, if you use <b>PASSWORD_DEFAULT</b>
 * you should store the resulting hash in a way that can store more than 60
 * characters (255 is the recommended width).
 * </p>
 * <p>
 * Values for this constant:
 * </p>
 * <ul>
 * <li>
 * PHP 5.5.0 - <b>PASSWORD_BCRYPT</b>
 * </li>
 * </ul>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 25,
    'endLine' => 25,
    'startColumn' => 1,
    'endColumn' => 31,
    'namespace' => NULL,
  ),
));