<?php declare(strict_types = 1);

// osfsl-C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/Dsn.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Sentry\Dsn
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-90146e131ce51b612895418077422ea81d1c7f3ec8cc64dbed99763f6764ce93-8.2.12-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Sentry\\Dsn',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/Dsn.php',
      ),
    ),
    'namespace' => 'Sentry',
    'name' => 'Sentry\\Dsn',
    'shortName' => 'Dsn',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * This class represents a Sentry DSN that can be obtained from the Settings
 * page of a project.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 244,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Stringable',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'SENTRY_ORG_ID_REGEX' => 
      array (
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'name' => 'SENTRY_ORG_ID_REGEX',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'/^o(\\d+)\\./\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 39,
            'startFilePos' => 468,
            'endTokenPos' => 39,
            'endFilePos' => 480,
          ),
        ),
        'docComment' => '/**
 * @var string Regex to match the organization ID in the host.
 *             This only applies to Sentry SaaS DSNs that contain the organization ID.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
    ),
    'immediateProperties' => 
    array (
      'scheme' => 
      array (
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'name' => 'scheme',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var string The protocol to be used to access the resource
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 20,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'host' => 
      array (
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'name' => 'host',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var string The host that holds the resource
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 18,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'port' => 
      array (
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'name' => 'port',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var int The port on which the resource is exposed
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 18,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'publicKey' => 
      array (
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'name' => 'publicKey',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var string The public key to authenticate the SDK
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'projectId' => 
      array (
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'name' => 'projectId',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var string The ID of the resource to access
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'path' => 
      array (
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'name' => 'path',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var string The specific resource that the web client wants to access
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 18,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'orgId' => 
      array (
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'name' => 'orgId',
        'modifiers' => 4,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var int|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 19,
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
          'scheme' => 
          array (
            'name' => 'scheme',
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
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 34,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'host' => 
          array (
            'name' => 'host',
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
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 50,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'port' => 
          array (
            'name' => 'port',
            'default' => NULL,
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
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 64,
            'endColumn' => 72,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'projectId' => 
          array (
            'name' => 'projectId',
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
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 75,
            'endColumn' => 91,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 94,
            'endColumn' => 105,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'publicKey' => 
          array (
            'name' => 'publicKey',
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
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 108,
            'endColumn' => 124,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
          'orgId' => 
          array (
            'name' => 'orgId',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 67,
                'endLine' => 67,
                'startTokenPos' => 136,
                'startFilePos' => 1809,
                'endTokenPos' => 136,
                'endFilePos' => 1812,
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
                      'name' => 'int',
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
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 127,
            'endColumn' => 144,
            'parameterIndex' => 6,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Class constructor.
 *
 * @param string $scheme    The protocol to be used to access the resource
 * @param string $host      The host that holds the resource
 * @param int    $port      The port on which the resource is exposed
 * @param string $projectId The ID of the resource to access
 * @param string $path      The specific resource that the web client wants to access
 * @param string $publicKey The public key to authenticate the SDK
 * @param ?int   $orgId     The org ID
 */',
        'startLine' => 67,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'createFromString' => 
      array (
        'name' => 'createFromString',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
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
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 45,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Creates an instance of this class by parsing the given string.
 *
 * @param string $value The string to parse
 */',
        'startLine' => 83,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getScheme' => 
      array (
        'name' => 'getScheme',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the protocol to be used to access the resource.
 */',
        'startLine' => 129,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getHost' => 
      array (
        'name' => 'getHost',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the host that holds the resource.
 */',
        'startLine' => 137,
        'endLine' => 140,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getPort' => 
      array (
        'name' => 'getPort',
        'parameters' => 
        array (
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
 * Gets the port on which the resource is exposed.
 */',
        'startLine' => 145,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getPath' => 
      array (
        'name' => 'getPath',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the specific resource that the web client wants to access.
 */',
        'startLine' => 153,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getProjectId' => 
      array (
        'name' => 'getProjectId',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the ID of the resource to access.
 */',
        'startLine' => 161,
        'endLine' => 164,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getPublicKey' => 
      array (
        'name' => 'getPublicKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gets the public key to authenticate the SDK.
 */',
        'startLine' => 169,
        'endLine' => 172,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getOrgId' => 
      array (
        'name' => 'getOrgId',
        'parameters' => 
        array (
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
                  'name' => 'int',
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
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 174,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getEnvelopeApiEndpointUrl' => 
      array (
        'name' => 'getEnvelopeApiEndpointUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the URL of the API for the envelope endpoint.
 */',
        'startLine' => 182,
        'endLine' => 185,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getCspReportEndpointUrl' => 
      array (
        'name' => 'getCspReportEndpointUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the URL of the API for the CSP report endpoint.
 */',
        'startLine' => 190,
        'endLine' => 193,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getOtlpTracesEndpointUrl' => 
      array (
        'name' => 'getOtlpTracesEndpointUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the URL of the API for the OTLP traces endpoint.
 */',
        'startLine' => 198,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      '__toString' => 
      array (
        'name' => '__toString',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @see https://www.php.net/manual/en/language.oop5.magic.php#object.tostring
 */',
        'startLine' => 206,
        'endLine' => 223,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
        'aliasName' => NULL,
      ),
      'getBaseEndpointUrl' => 
      array (
        'name' => 'getBaseEndpointUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the base url to Sentry from the DSN.
 */',
        'startLine' => 228,
        'endLine' => 243,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Sentry',
        'declaringClassName' => 'Sentry\\Dsn',
        'implementingClassName' => 'Sentry\\Dsn',
        'currentClassName' => 'Sentry\\Dsn',
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