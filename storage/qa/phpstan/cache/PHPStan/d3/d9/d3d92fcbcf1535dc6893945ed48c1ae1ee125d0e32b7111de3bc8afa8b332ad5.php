<?php declare(strict_types = 1);

// odsl-C:\xampp\htdocs\albatechsolutions\app\Modules\Payments\Gateway\PaymentGatewayInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Modules\Payments\Gateway\PaymentGatewayInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-ffa479511e37994a16360dc3eb0353b3c1894c5efe327e5b3fb7759c339db29e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/app/Modules/Payments/Gateway/PaymentGatewayInterface.php',
      ),
    ),
    'namespace' => 'App\\Modules\\Payments\\Gateway',
    'name' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
    'shortName' => 'PaymentGatewayInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Provider abstraction. The Payments module does not know whether the
 * underlying provider is Paystack, Daraja, Stripe, Flutterwave, etc.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 42,
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
      'initialize' => 
      array (
        'name' => 'initialize',
        'parameters' => 
        array (
          'payment' => 
          array (
            'name' => 'payment',
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
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 32,
            'endColumn' => 45,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param array{
 *   reference:string,
 *   amount:int,
 *   currency:string,
 *   email:string,
 *   phone?:string|null,
 *   description?:string|null,
 *   metadata?:array<string,mixed>,
 *   callback_url?:string|null
 * } $payment
 * @return array{success:bool, authorization_url?:string, access_code?:string, reference?:string, message?:string}
 */',
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 54,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Modules\\Payments\\Gateway',
        'declaringClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'implementingClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'currentClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'aliasName' => NULL,
      ),
      'verify' => 
      array (
        'name' => 'verify',
        'parameters' => 
        array (
          'reference' => 
          array (
            'name' => 'reference',
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
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 28,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array{success:bool, data?:array<string,mixed>, message?:string}
 */',
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 53,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Modules\\Payments\\Gateway',
        'declaringClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'implementingClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'currentClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'aliasName' => NULL,
      ),
      'refund' => 
      array (
        'name' => 'refund',
        'parameters' => 
        array (
          'reference' => 
          array (
            'name' => 'reference',
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 28,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'amountSubunit' => 
          array (
            'name' => 'amountSubunit',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 41,
                'endLine' => 41,
                'startTokenPos' => 77,
                'startFilePos' => 1289,
                'endTokenPos' => 77,
                'endFilePos' => 1292,
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 47,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'reason' => 
          array (
            'name' => 'reason',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 41,
                'endLine' => 41,
                'startTokenPos' => 87,
                'startFilePos' => 1313,
                'endTokenPos' => 87,
                'endFilePos' => 1316,
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 75,
            'endColumn' => 96,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Initiates a refund on the provider. A full refund of the original
 * transaction unless a smaller amount is given. Refunds are async on
 * Paystack\'s side — a successful response here means the request was
 * accepted, not that funds have moved yet.
 *
 * @return array{success:bool, message?:string}
 */',
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 105,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Modules\\Payments\\Gateway',
        'declaringClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'implementingClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
        'currentClassName' => 'App\\Modules\\Payments\\Gateway\\PaymentGatewayInterface',
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