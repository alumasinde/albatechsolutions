<?php declare(strict_types = 1);

// odsl-C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/functions.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-Sentry\init
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.2.12-3bdda3598d2cb0ab79242d1348a2bfa6b02b8fd70a1b2be630ae0b1ea494eef6',
   'data' => 
  array (
    'name' => 'init',
    'parameters' => 
    array (
      'options' => 
      array (
        'name' => 'options',
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 92,
            'startFilePos' => 2598,
            'endTokenPos' => 93,
            'endFilePos' => 2599,
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
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 15,
        'endColumn' => 33,
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
        'name' => 'void',
        'isIdentifier' => true,
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Creates a new Client and Hub which will be set as current.
 *
 * @param array{
 *     attach_metric_code_locations?: bool,
 *     attach_stacktrace?: bool,
 *     before_breadcrumb?: callable,
 *     before_send?: callable,
 *     before_send_check_in?: callable,
 *     before_send_log?: callable,
 *     before_send_transaction?: callable,
 *     capture_silenced_errors?: bool,
 *     context_lines?: int|null,
 *     default_integrations?: bool,
 *     dsn?: string|bool|Dsn|null,
 *     enable_logs?: bool,
 *     environment?: string|null,
 *     error_types?: int|null,
 *     http_client?: HttpClientInterface|null,
 *     http_compression?: bool,
 *     http_connect_timeout?: int|float,
 *     http_proxy?: string|null,
 *     http_proxy_authentication?: string|null,
 *     http_ssl_verify_peer?: bool,
 *     http_timeout?: int|float,
 *     http_enable_curl_share_handle?: bool,
 *     ignore_exceptions?: array<class-string>,
 *     ignore_transactions?: array<string>,
 *     in_app_exclude?: array<string>,
 *     in_app_include?: array<string>,
 *     integrations?: IntegrationInterface[]|callable(IntegrationInterface[]): IntegrationInterface[],
 *     logger?: LoggerInterface|null,
 *     log_flush_threshold?: int|null,
 *     metric_flush_threshold?: int|null,
 *     max_breadcrumbs?: int,
 *     max_request_body_size?: "none"|"never"|"small"|"medium"|"always",
 *     max_value_length?: int,
 *     org_id?: int|null,
 *     prefixes?: array<string>,
 *     profiles_sample_rate?: int|float|null,
 *     profiles_sampler?: callable|null,
 *     release?: string|null,
 *     sample_rate?: float|int,
 *     send_attempts?: int,
 *     send_default_pii?: bool,
 *     server_name?: string,
 *     spotlight?: bool,
 *     spotlight_url?: string,
 *     strict_trace_continuation?: bool,
 *     tags?: array<string>,
 *     trace_propagation_targets?: array<string>|null,
 *     traces_sample_rate?: float|int|null,
 *     traces_sampler?: callable|null,
 *     transport?: TransportInterface|null,
 * } $options The client options
 */',
    'startLine' => 77,
    'endLine' => 82,
    'startColumn' => 1,
    'endColumn' => 1,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => 'Sentry',
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Sentry\\init',
        'filename' => 'C:/xampp/htdocs/albatechsolutions/vendor/composer/../sentry/sentry/src/functions.php',
      ),
    ),
  ),
));