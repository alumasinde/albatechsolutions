<?php

declare(strict_types=1);

namespace App\Core\Helpers;

/**
 * Validator - lightweight rule-based validation.
 *
 * Usage:
 *   $v = new Validator($data, [
 *       'email' => 'required|email',
 *       'name'  => 'required|min:2|max:100',
 *       'phone' => 'required|regex:/^254[0-9]{9}$/',
 *   ]);
 *   if (!$v->passes()) { $errors = $v->errors(); }
 */
final class Validator
{
    private array $errors = [];

    public function __construct(
        private readonly array $data,
        private readonly array $rules
    ) {
        $this->run();
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);

        $fails = match ($name) {
            'required' => $value === null || $value === '',
            'email'    => $value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL),
            'numeric'  => $value !== null && $value !== '' && !is_numeric($value),
            'min'      => $value !== null && $value !== '' && strlen((string) $value) < (int) $param,
            'max'      => $value !== null && $value !== '' && strlen((string) $value) > (int) $param,
            'regex'    => $value !== null && $value !== '' && !preg_match($param, (string) $value),
            'in'       => $value !== null && $value !== '' && !in_array($value, explode(',', $param), true),
            'confirmed' => $value !== ($this->data[$field . '_confirmation'] ?? null),
            default    => false,
        };

        if ($fails) {
            $this->errors[$field][] = $this->message($field, $name, $param);
        }
    }

    private function message(string $field, string $rule, ?string $param): string
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        return match ($rule) {
            'required'  => "{$label} is required.",
            'email'     => "{$label} must be a valid email address.",
            'numeric'   => "{$label} must be numeric.",
            'min'       => "{$label} must be at least {$param} characters.",
            'max'       => "{$label} must not exceed {$param} characters.",
            'regex'     => "{$label} format is invalid.",
            'in'        => "{$label} must be one of: {$param}.",
            'confirmed' => "{$label} confirmation does not match.",
            default     => "{$label} is invalid.",
        };
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }

        return null;
    }
}
