# AlbaTech QA & Testing

## Fast local check

From the project root:

```powershell
php bin/qa.php
```

This does not require PHPUnit or PHPStan. It checks:

- PHP syntax across application PHP files
- duplicate named PDO placeholders in literal `prepare()` statements
- Composer autoload loading
- optional PHPStan/PHPUnit hooks when those tools are installed

The PDO check is specifically designed to catch the class of bug that caused:

`SQLSTATE[HY093]: Invalid parameter number`

For example, this is unsafe with native MySQL PDO:

```php
WHERE a >= :days
   OR b >= :days
```

Use unique placeholders instead:

```php
WHERE a >= :a_days
   OR b >= :b_days
```

The V4.0 `Database` class intentionally uses:

```php
PDO::ATTR_EMULATE_PREPARES => false
```

so native PDO behaviour should be tested rather than hidden.

## Composer shortcut

```powershell
composer qa
```

For only the PDO/static audit:

```powershell
composer qa:sql
```

## Add PHPStan

PHPStan is optional but recommended:

```powershell
composer require --dev phpstan/phpstan
php bin/qa.php --phpstan
```

The project includes `phpstan.neon.dist`.

PHPStan catches a different class of problems: type errors, invalid calls, undefined properties/variables and control-flow issues. It is not a replacement for executing SQL.

## Add PHPUnit

```powershell
composer require --dev phpunit/phpunit
```

Then:

```powershell
vendor/bin/phpunit
```

or:

```powershell
php bin/qa.php --phpunit
```

The project includes `phpunit.xml.dist` and an integration test for the Growth repository. The integration suite requires a configured local/test database with migrations applied.

## Recommended CI order

```text
1. composer validate
2. composer qa
3. PHPStan
4. PHPUnit unit/integration tests
5. migrations against a clean test database
6. browser/E2E tests
7. production preflight
```

Do not use production customer data as the test database.

## Current V4.0 fix

`GrowthAnalyticsRepository::servicePerformance()` previously reused `:days` in two separate subqueries. With native MySQL PDO prepares this can raise HY093.

It now uses:

- `:views_days`
- `:events_days`
- `:limit`

and binds each explicitly.
