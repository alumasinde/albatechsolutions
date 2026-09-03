<?php declare(strict_types = 1);

// osfsl-C:/xampp/htdocs/albatechsolutions/vendor/composer/../php-di/php-di/src/ContainerBuilder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-DI\ContainerBuilder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-cae21353e65a68b5e6a635275006dc4fd4a920671e825b0718fda6efc5d7bc81-8.2.12-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'DI\\ContainerBuilder',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/vendor/composer/../php-di/php-di/src/ContainerBuilder.php',
      ),
    ),
    'namespace' => 'DI',
    'name' => 'DI\\ContainerBuilder',
    'shortName' => 'ContainerBuilder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Helper to create and configure a Container.
 *
 * With the default options, the container created is appropriate for the development environment.
 *
 * Example:
 *
 *     $builder = new ContainerBuilder();
 *     $container = $builder->build();
 *
 * @api
 *
 * @since  3.2
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 *
 * @psalm-template ContainerClass of Container
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 38,
    'endLine' => 336,
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
      'containerClass' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'containerClass',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * Name of the container class, used to create the container.
 * @var class-string<Container>
 * @psalm-var class-string<ContainerClass>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'containerParentClass' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'containerParentClass',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * Name of the container parent class, used on compiled container.
 * @var class-string<Container>
 * @psalm-var class-string<ContainerClass>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'useAutowiring' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'useAutowiring',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 54,
            'endLine' => 54,
            'startTokenPos' => 114,
            'startFilePos' => 1405,
            'endTokenPos' => 114,
            'endFilePos' => 1408,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'useAttributes' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'useAttributes',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 56,
            'startTokenPos' => 125,
            'startFilePos' => 1446,
            'endTokenPos' => 125,
            'endFilePos' => 1450,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 56,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'proxyDirectory' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'proxyDirectory',
        'modifiers' => 4,
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
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 61,
            'endLine' => 61,
            'startTokenPos' => 139,
            'startFilePos' => 1592,
            'endTokenPos' => 139,
            'endFilePos' => 1595,
          ),
        ),
        'docComment' => '/**
 * If set, write the proxies to disk in this directory to improve performances.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'wrapperContainer' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'wrapperContainer',
        'modifiers' => 4,
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
                  'name' => 'Psr\\Container\\ContainerInterface',
                  'isIdentifier' => false,
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
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 66,
            'endLine' => 66,
            'startTokenPos' => 153,
            'startFilePos' => 1746,
            'endTokenPos' => 153,
            'endFilePos' => 1749,
          ),
        ),
        'docComment' => '/**
 * If PHP-DI is wrapped in another container, this references the wrapper.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 57,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'definitionSources' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'definitionSources',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 166,
            'startFilePos' => 1856,
            'endTokenPos' => 167,
            'endFilePos' => 1857,
          ),
        ),
        'docComment' => '/**
 * @var DefinitionSource[]|string[]|array[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'locked' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'locked',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 180,
            'startFilePos' => 1957,
            'endTokenPos' => 180,
            'endFilePos' => 1961,
          ),
        ),
        'docComment' => '/**
 * Whether the container has already been built.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'compileToDirectory' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'compileToDirectory',
        'modifiers' => 4,
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
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 192,
            'startFilePos' => 2007,
            'endTokenPos' => 192,
            'endFilePos' => 2010,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'sourceCache' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'sourceCache',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 203,
            'startFilePos' => 2046,
            'endTokenPos' => 203,
            'endFilePos' => 2050,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 38,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'sourceCacheNamespace' => 
      array (
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'name' => 'sourceCacheNamespace',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 214,
            'startFilePos' => 2099,
            'endTokenPos' => 214,
            'endFilePos' => 2100,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 48,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'containerClass' => 
          array (
            'name' => 'containerClass',
            'default' => 
            array (
              'code' => '\\DI\\Container::class',
              'attributes' => 
              array (
                'startLine' => 88,
                'endLine' => 88,
                'startTokenPos' => 231,
                'startFilePos' => 2355,
                'endTokenPos' => 233,
                'endFilePos' => 2370,
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
            'startLine' => 88,
            'endLine' => 88,
            'startColumn' => 33,
            'endColumn' => 73,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param class-string<Container> $containerClass Name of the container class, used to create the container.
 * @psalm-param class-string<ContainerClass> $containerClass
 */',
        'startLine' => 88,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'build' => 
      array (
        'name' => 'build',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Build and return a container.
 *
 * @return Container
 * @psalm-return ContainerClass
 */',
        'startLine' => 99,
        'endLine' => 162,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'enableCompilation' => 
      array (
        'name' => 'enableCompilation',
        'parameters' => 
        array (
          'directory' => 
          array (
            'name' => 'directory',
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
            'startLine' => 186,
            'endLine' => 186,
            'startColumn' => 9,
            'endColumn' => 25,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'containerClass' => 
          array (
            'name' => 'containerClass',
            'default' => 
            array (
              'code' => '\'CompiledContainer\'',
              'attributes' => 
              array (
                'startLine' => 187,
                'endLine' => 187,
                'startTokenPos' => 693,
                'startFilePos' => 5785,
                'endTokenPos' => 693,
                'endFilePos' => 5803,
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
            'startLine' => 187,
            'endLine' => 187,
            'startColumn' => 9,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'containerParentClass' => 
          array (
            'name' => 'containerParentClass',
            'default' => 
            array (
              'code' => '\\DI\\CompiledContainer::class',
              'attributes' => 
              array (
                'startLine' => 188,
                'endLine' => 188,
                'startTokenPos' => 702,
                'startFilePos' => 5845,
                'endTokenPos' => 704,
                'endFilePos' => 5868,
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
            'startLine' => 188,
            'endLine' => 188,
            'startColumn' => 9,
            'endColumn' => 63,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Compile the container for optimum performances.
 *
 * Be aware that the container is compiled once and never updated!
 *
 * Therefore:
 *
 * - in production you should clear that directory every time you deploy
 * - in development you should not compile the container
 *
 * @see https://php-di.org/doc/performances.html
 *
 * @psalm-template T of CompiledContainer
 *
 * @param string $directory Directory in which to put the compiled container.
 * @param string $containerClass Name of the compiled class. Customize only if necessary.
 * @param class-string<Container> $containerParentClass Name of the compiled container parent class. Customize only if necessary.
 * @psalm-param class-string<T> $containerParentClass
 *
 * @psalm-return self<T>
 */',
        'startLine' => 185,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'useAutowiring' => 
      array (
        'name' => 'useAutowiring',
        'parameters' => 
        array (
          'bool' => 
          array (
            'name' => 'bool',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 206,
            'endLine' => 206,
            'startColumn' => 35,
            'endColumn' => 44,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enable or disable the use of autowiring to guess injections.
 *
 * Enabled by default.
 *
 * @return $this
 */',
        'startLine' => 206,
        'endLine' => 213,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'useAttributes' => 
      array (
        'name' => 'useAttributes',
        'parameters' => 
        array (
          'bool' => 
          array (
            'name' => 'bool',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 222,
            'endLine' => 222,
            'startColumn' => 35,
            'endColumn' => 44,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enable or disable the use of PHP 8 attributes to configure injections.
 *
 * Disabled by default.
 *
 * @return $this
 */',
        'startLine' => 222,
        'endLine' => 229,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'writeProxiesToFile' => 
      array (
        'name' => 'writeProxiesToFile',
        'parameters' => 
        array (
          'writeToFile' => 
          array (
            'name' => 'writeToFile',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 40,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'proxyDirectory' => 
          array (
            'name' => 'proxyDirectory',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 244,
                'endLine' => 244,
                'startTokenPos' => 860,
                'startFilePos' => 7411,
                'endTokenPos' => 860,
                'endFilePos' => 7414,
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
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 59,
            'endColumn' => 88,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Configure the proxy generation.
 *
 * For dev environment, use `writeProxiesToFile(false)` (default configuration)
 * For production environment, use `writeProxiesToFile(true, \'tmp/proxies\')`
 *
 * @see https://php-di.org/doc/lazy-injection.html
 *
 * @param bool $writeToFile If true, write the proxies to disk to improve performances
 * @param string|null $proxyDirectory Directory where to write the proxies
 * @return $this
 * @throws InvalidArgumentException when writeToFile is set to true and the proxy directory is null
 */',
        'startLine' => 244,
        'endLine' => 256,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'wrapContainer' => 
      array (
        'name' => 'wrapContainer',
        'parameters' => 
        array (
          'otherContainer' => 
          array (
            'name' => 'otherContainer',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Psr\\Container\\ContainerInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 264,
            'endLine' => 264,
            'startColumn' => 35,
            'endColumn' => 68,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * If PHP-DI\'s container is wrapped by another container, we can
 * set this so that PHP-DI will use the wrapper rather than itself for building objects.
 *
 * @return $this
 */',
        'startLine' => 264,
        'endLine' => 271,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'addDefinitions' => 
      array (
        'name' => 'addDefinitions',
        'parameters' => 
        array (
          'definitions' => 
          array (
            'name' => 'definitions',
            'default' => NULL,
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
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'DI\\Definition\\Source\\DefinitionSource',
                      'isIdentifier' => false,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 281,
            'endLine' => 281,
            'startColumn' => 36,
            'endColumn' => 80,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add definitions to the container.
 *
 * @param string|array|DefinitionSource ...$definitions Can be an array of definitions, the
 *                                                      name of a file containing definitions
 *                                                      or a DefinitionSource object.
 * @return $this
 */',
        'startLine' => 281,
        'endLine' => 290,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'enableDefinitionCache' => 
      array (
        'name' => 'enableDefinitionCache',
        'parameters' => 
        array (
          'cacheNamespace' => 
          array (
            'name' => 'cacheNamespace',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 312,
                'endLine' => 312,
                'startTokenPos' => 1049,
                'startFilePos' => 10019,
                'endTokenPos' => 1049,
                'endFilePos' => 10020,
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
            'startLine' => 312,
            'endLine' => 312,
            'startColumn' => 43,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enables the use of APCu to cache definitions.
 *
 * You must have APCu enabled to use it.
 *
 * Before using this feature, you should try these steps first:
 * - enable compilation if not already done (see `enableCompilation()`)
 * - if you use autowiring or attributes, add all the classes you are using into your configuration so that
 *   PHP-DI knows about them and compiles them
 * Once this is done, you can try to optimize performances further with APCu. It can also be useful if you use
 * `Container::make()` instead of `get()` (`make()` calls cannot be compiled so they are not optimized).
 *
 * Remember to clear APCu on each deploy else your application will have a stale cache. Do not enable the cache
 * in development environment: any change you will make to the code will be ignored because of the cache.
 *
 * @see https://php-di.org/doc/performances.html
 *
 * @param string $cacheNamespace use unique namespace per container when sharing a single APC memory pool to prevent cache collisions
 * @return $this
 */',
        'startLine' => 312,
        'endLine' => 320,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'isCompilationEnabled' => 
      array (
        'name' => 'isCompilationEnabled',
        'parameters' => 
        array (
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
 * Are we building a compiled container?
 */',
        'startLine' => 325,
        'endLine' => 328,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
        'aliasName' => NULL,
      ),
      'ensureNotLocked' => 
      array (
        'name' => 'ensureNotLocked',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 330,
        'endLine' => 335,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'DI',
        'declaringClassName' => 'DI\\ContainerBuilder',
        'implementingClassName' => 'DI\\ContainerBuilder',
        'currentClassName' => 'DI\\ContainerBuilder',
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