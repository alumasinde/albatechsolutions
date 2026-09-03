<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\albatechsolutions\app\Modules\Auth\Service\GoogleOAuthService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Modules\Auth\Service\GoogleOAuthService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-c2b8ed6d0fb788d74c10408a51ad49854f50edfdccf33f709f0a5dff6fb76111',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/app/Modules/Auth/Service/GoogleOAuthService.php',
      ),
    ),
    'namespace' => 'App\\Modules\\Auth\\Service',
    'name' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
    'shortName' => 'GoogleOAuthService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 102,
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
      'AUTH_URL' => 
      array (
        'declaringClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'implementingClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'name' => 'AUTH_URL',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'https://accounts.google.com/o/oauth2/v2/auth\'',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 13,
            'startTokenPos' => 46,
            'startFilePos' => 198,
            'endTokenPos' => 46,
            'endFilePos' => 243,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 5,
        'endColumn' => 76,
      ),
      'TOKEN_URL' => 
      array (
        'declaringClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'implementingClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'name' => 'TOKEN_URL',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'https://oauth2.googleapis.com/token\'',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 14,
            'startTokenPos' => 57,
            'startFilePos' => 276,
            'endTokenPos' => 57,
            'endFilePos' => 312,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 5,
        'endColumn' => 68,
      ),
      'USERINFO_URL' => 
      array (
        'declaringClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'implementingClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'name' => 'USERINFO_URL',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'https://www.googleapis.com/oauth2/v3/userinfo\'',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 15,
            'startTokenPos' => 68,
            'startFilePos' => 348,
            'endTokenPos' => 68,
            'endFilePos' => 394,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 81,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'authorizationUrl' => 
      array (
        'name' => 'authorizationUrl',
        'parameters' => 
        array (
          'state' => 
          array (
            'name' => 'state',
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
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 38,
            'endColumn' => 50,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 17,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Modules\\Auth\\Service',
        'declaringClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'implementingClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'currentClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'aliasName' => NULL,
      ),
      'handleCallback' => 
      array (
        'name' => 'handleCallback',
        'parameters' => 
        array (
          'code' => 
          array (
            'name' => 'code',
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
            'startLine' => 35,
            'endLine' => 35,
            'startColumn' => 36,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
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
                  'name' => 'array',
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
        'docComment' => '/**
 * @return array{email: string, name: string, google_id: string}|null
 */',
        'startLine' => 35,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Modules\\Auth\\Service',
        'declaringClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'implementingClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'currentClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'aliasName' => NULL,
      ),
      'exchangeCodeForToken' => 
      array (
        'name' => 'exchangeCodeForToken',
        'parameters' => 
        array (
          'code' => 
          array (
            'name' => 'code',
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
            'startLine' => 46,
            'endLine' => 46,
            'startColumn' => 43,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
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
        'startLine' => 46,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Modules\\Auth\\Service',
        'declaringClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'implementingClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'currentClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'aliasName' => NULL,
      ),
      'fetchProfile' => 
      array (
        'name' => 'fetchProfile',
        'parameters' => 
        array (
          'accessToken' => 
          array (
            'name' => 'accessToken',
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
            'startLine' => 74,
            'endLine' => 74,
            'startColumn' => 35,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
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
                  'name' => 'array',
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
        'docComment' => '/**
 * @return array{email: string, name: string, google_id: string}|null
 */',
        'startLine' => 74,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Modules\\Auth\\Service',
        'declaringClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'implementingClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
        'currentClassName' => 'App\\Modules\\Auth\\Service\\GoogleOAuthService',
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