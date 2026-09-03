<?php declare(strict_types = 1);

// osfsl-C:/xampp/htdocs/albatechsolutions/vendor/composer/../php-di/php-di/src/FactoryInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-DI\FactoryInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f997e4429e25a1828a559a8e85afe72c83de60563d4f8b714f6b21b10f7b15c5-8.2.12-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'DI\\FactoryInterface',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/vendor/composer/../php-di/php-di/src/FactoryInterface.php',
      ),
    ),
    'namespace' => 'DI',
    'name' => 'DI\\FactoryInterface',
    'shortName' => 'FactoryInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Describes the basic interface of a factory.
 *
 * @api
 *
 * @since 4.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 30,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'make' => 
      array (
        'name' => 'make',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 26,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 29,
                'endLine' => 29,
                'startTokenPos' => 42,
                'startFilePos' => 981,
                'endTokenPos' => 43,
                'endFilePos' => 982,
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 40,
            'endColumn' => 61,
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
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolves an entry by its name. If given a class name, it will return a new instance of that class.
 *
 * @param string $name       Entry name or a class name.
 * @param array  $parameters Optional parameters to use to build the entry. Use this to force specific
 *                           parameters to specific values. Parameters not defined in this array will
 *                           be automatically resolved.
 *
 * @throws \\InvalidArgumentException The name parameter must be of type string.
 * @throws DependencyException       Error while resolving the entry.
 * @throws NotFoundException         No entry or class found for the given name.
 */',
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 71,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\FactoryInterface',
        'implementingClassName' => 'DI\\FactoryInterface',
        'currentClassName' => 'DI\\FactoryInterface',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));