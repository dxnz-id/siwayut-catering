<?php
declare(strict_types=1);
// File: src/Core/Validator.php

namespace App\Core;
use PDO;

class Validator {
    private array $errors = [];

    public function __construct(private ?PDO $db = null) {
    }

    public function validate(array $data, array $rules): bool {
        $this->errors = [];
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $value = $data[$field] ?? null;
            foreach ($ruleList as $rule) {
                if (!$this->applyRule($field, $value, trim($rule), $data)) {
                    break; // stop on first failure for this field
                }
            }
        }
        return empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function error(string $field): ?string {
        return $this->errors[$field] ?? null;
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    // CONTRACT: Rule parsing MUST use: explode(':', $rule, 3)
    // For "in:" rules, argument parsing MUST use: explode(',', $argument)
    private function applyRule(string $field, mixed $value, string $rule, array $data): bool {
        $parts = explode(':', $rule, 3);
        $ruleName = $parts[0];
        $argument = $parts[1] ?? null;

        $label = str_replace('_', ' ', $field);

        switch ($ruleName) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->errors[$field] = __('validation_required', ['field' => $label]);
                    return false;
                }
                return true;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = __('validation_email', ['field' => $label]);
                    return false;
                }
                return true;

            case 'min':
                $min = (int) $argument;
                if (is_string($value) && mb_strlen($value) < $min) {
                    $this->errors[$field] = __('validation_min', ['field' => $label, 'min' => $min]);
                    return false;
                }
                return true;

            case 'max':
                $max = (int) $argument;
                if (is_string($value) && mb_strlen($value) > $max) {
                    $this->errors[$field] = __('validation_max', ['field' => $label, 'max' => $max]);
                    return false;
                }
                return true;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (($data[$confirmField] ?? null) !== $value) {
                    $this->errors[$field] = __('validation_confirmed', ['field' => $label]);
                    return false;
                }
                return true;

            case 'unique':
                // argument format: table,column
                if ($this->db !== null && $argument !== null) {
                    $argParts = explode(',', $argument);
                    $table = $argParts[0];
                    $column = $argParts[1] ?? $field;
                    $exceptId = $argParts[2] ?? null;

                    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
                        return true;
                    }
                    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                        $column = $field;
                    }

                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
                    $params = [$value];
                    if ($exceptId !== null) {
                        $sql .= " AND `id` != ?";
                        $params[] = $exceptId;
                    }
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($params);
                    if ((int) $stmt->fetchColumn() > 0) {
                        $this->errors[$field] = __('validation_unique', ['field' => $label]);
                        return false;
                    }
                }
                return true;

            case 'in':
                if ($argument !== null) {
                    $allowed = explode(',', $argument);
                    if (!in_array((string) $value, $allowed, true)) {
                        $this->errors[$field] = __('validation_in', ['field' => $label]);
                        return false;
                    }
                }
                return true;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->errors[$field] = __('validation_numeric', ['field' => $label]);
                    return false;
                }
                return true;

            case 'string':
                if ($value !== null && !is_string($value)) {
                    $this->errors[$field] = __('validation_string', ['field' => $label]);
                    return false;
                }
                return true;

            case 'nullable':
                // If value is null or empty, skip remaining rules
                if ($value === null || $value === '') {
                    return false; // false to break rule chain, but no error added
                }
                return true;

            case 'after_or_equal':
                $compare = $argument === 'today' ? date('Y-m-d') : $argument;
                if ($value !== null && $value !== '' && $value < $compare) {
                    $this->errors[$field] = __('validation_after_or_equal', ['field' => $label]);
                    return false;
                }
                return true;

            default:
                return true;
        }
    }
}
