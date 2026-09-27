<?php

namespace App\Utils;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $validated = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function passes(): bool
    {
        $this->errors = [];
        $this->validated = [];

        foreach ($this->rules as $field => $rules) {
            $ruleList = is_array($rules) ? $rules : explode('|', $rules);
            $value = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                if (!$this->applyRule($field, $value, $rule)) {
                    break;
                }
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule): bool
    {
        [$ruleName, $parameters] = $this->parseRule($rule);

        return match ($ruleName) {
            'required' => $this->validateRequired($field, $value),
            'string' => $this->validateString($field, $value),
            'email' => $this->validateEmail($field, $value),
            'min' => $this->validateMin($field, $value, $parameters[0] ?? 0),
            'max' => $this->validateMax($field, $value, $parameters[0] ?? PHP_INT_MAX),
            'numeric' => $this->validateNumeric($field, $value),
            'integer' => $this->validateInteger($field, $value),
            'boolean' => $this->validateBoolean($field, $value),
            'in' => $this->validateIn($field, $value, $parameters),
            'confirmed' => $this->validateConfirmed($field, $value),
            'date' => $this->validateDate($field, $value),
            'after' => $this->validateAfter($field, $value, $parameters[0] ?? ''),
            'before' => $this->validateBefore($field, $value, $parameters[0] ?? ''),
            default => true,
        };
    }

    private function parseRule(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$name, $params] = explode(':', $rule, 2);
            return [$name, explode(',', $params)];
        }
        return [$rule, []];
    }

    private function validateRequired(string $field, mixed $value): bool
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->errors[$field][] = "The {$field} field is required.";
            return false;
        }
        $this->validated[$field] = $value;
        return true;
    }

    private function validateString(string $field, mixed $value): bool
    {
        if (!is_string($value)) {
            $this->errors[$field][] = "The {$field} must be a string.";
            return false;
        }
        return true;
    }

    private function validateEmail(string $field, mixed $value): bool
    {
        if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "The {$field} must be a valid email address.";
            return false;
        }
        return true;
    }

    private function validateMin(string $field, mixed $value, int $min): bool
    {
        $length = is_string($value) ? strlen($value) : (is_array($value) ? count($value) : $value);
        if ($length < $min) {
            $this->errors[$field][] = "The {$field} must be at least {$min} characters.";
            return false;
        }
        return true;
    }

    private function validateMax(string $field, mixed $value, int $max): bool
    {
        $length = is_string($value) ? strlen($value) : (is_array($value) ? count($value) : $value);
        if ($length > $max) {
            $this->errors[$field][] = "The {$field} must not exceed {$max} characters.";
            return false;
        }
        return true;
    }

    private function validateNumeric(string $field, mixed $value): bool
    {
        if (!is_numeric($value)) {
            $this->errors[$field][] = "The {$field} must be numeric.";
            return false;
        }
        return true;
    }

    private function validateInteger(string $field, mixed $value): bool
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field][] = "The {$field} must be an integer.";
            return false;
        }
        return true;
    }

    private function validateBoolean(string $field, mixed $value): bool
    {
        if (!is_bool($value) && !in_array($value, [0, 1, '0', '1', 'true', 'false'], true)) {
            $this->errors[$field][] = "The {$field} must be true or false.";
            return false;
        }
        return true;
    }

    private function validateIn(string $field, mixed $value, array $values): bool
    {
        if (!in_array($value, $values, true)) {
            $this->errors[$field][] = "The selected {$field} is invalid.";
            return false;
        }
        return true;
    }

    private function validateConfirmed(string $field, mixed $value): bool
    {
        $confirmedField = $field . '_confirmation';
        if (($this->data[$confirmedField] ?? null) !== $value) {
            $this->errors[$field][] = "The {$field} confirmation does not match.";
            return false;
        }
        return true;
    }

    private function validateDate(string $field, mixed $value): bool
    {
        if (!is_string($value) || !$this->isValidDate($value)) {
            $this->errors[$field][] = "The {$field} is not a valid date.";
            return false;
        }
        return true;
    }

    private function validateAfter(string $field, mixed $value, string $date): bool
    {
        if (!$this->isValidDate($value) || !$this->isValidDate($date)) {
            return true;
        }
        if (strtotime($value) <= strtotime($date)) {
            $this->errors[$field][] = "The {$field} must be after {$date}.";
            return false;
        }
        return true;
    }

    private function validateBefore(string $field, mixed $value, string $date): bool
    {
        if (!$this->isValidDate($value) || !$this->isValidDate($date)) {
            return true;
        }
        if (strtotime($value) >= strtotime($date)) {
            $this->errors[$field][] = "The {$field} must be before {$date}.";
            return false;
        }
        return true;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        if ($d) return true;
        
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if ($d) return true;
        
        $d = \DateTime::createFromFormat('Y-m-d\TH:i', $date);
        return $d !== false;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->validated;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }
}