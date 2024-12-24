<?php
// src/Validators/Validator.php

namespace MVC\Validators;

class Validator {
    private $data;
    private $rules;
    private $errors = [];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): bool {
        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                $this->validateField($field, $rule);
            }
        }
        return empty($this->errors);
    }

    private function validateField(string $field, string $rule): void {
        $value = $this->data[$field] ?? null;

        if (strpos($rule, ':') !== false) {
            [$ruleName, $parameter] = explode(':', $rule);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, ucfirst($field) . ' is required');
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Invalid email format');
                }
                break;

            case 'min':
                if (strlen($value) < intval($parameter)) {
                    $this->addError($field, ucfirst($field) . " must be at least $parameter characters");
                }
                break;

            case 'phone':
                if (!preg_match('/^[0-9]{10}$/', $value)) {
                    $this->addError($field, 'Phone number must be 10 digits');
                }
                break;

            case 'alpha_space':
                if (!preg_match('/^[a-zA-Z\s]+$/', $value)) {
                    $this->addError($field, ucfirst($field) . ' can only contain letters and spaces');
                }
                break;

            case 'same':
                if ($value !== ($this->data[$parameter] ?? null)) {
                    $this->addError($field, ucfirst($field) . " must match $parameter");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function getFirstError(): ?string {
        foreach ($this->errors as $fieldErrors) {
            return reset($fieldErrors);
        }
        return null;
    }
}