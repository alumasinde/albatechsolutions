<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\albatechsolutions\app\Modules\Payments\Controller\MpesaCallbackController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Modules\Payments\Controller\MpesaCallbackController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-b1f9c7623328087f1fd23e56c5e975af11a61d051761006cdaafd9b8ffc1fc4b',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/app/Modules/Payments/Controller/MpesaCallbackController.php',
      ),
    ),
    'namespace' => 'App\\Modules\\Payments\\Controller',
    'name' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
    'shortName' => 'MpesaCallbackController',
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
    'endLine' => 45,
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
      'paymentService' => 
      array (
        'declaringClassName' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
        'implementingClassName' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
        'name' => 'paymentService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Modules\\Payments\\Service\\PaymentService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 9,
        'endColumn' => 55,
        'isPromoted' => true,
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
          'paymentService' => 
          array (
            'name' => 'paymentService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Modules\\Payments\\Service\\PaymentService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 9,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 13,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Modules\\Payments\\Controller',
        'declaringClassName' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
        'implementingClassName' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
        'currentClassName' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Core\\Response',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Safaricom posts a JSON body here — never a browser request, so
 * this route deliberately sits outside auth/CSRF middleware. We
 * read the raw body directly rather than relying on Request\'s
 * form-parsing, since Daraja\'s content-type header isn\'t always
 * exactly application/x-www-form-urlencoded or application/json
 * in a way our generic Request class expects.
 */',
        'startLine' => 26,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Modules\\Payments\\Controller',
        'declaringClassName' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
        'implementingClassName' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
        'currentClassName' => 'App\\Modules\\Payments\\Controller\\MpesaCallbackController',
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