<?php

namespace App\Services\Validators;

use App\Services\Validators\Validation;
use Psr\Container\ContainerInterface;

class Validator extends Validation
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function validate($rules)
    {
        foreach ($rules as $field => $validation) {
            // Primeiro, processa validações com parâmetros
            $validation = $this->validateWithParameter($field, $validation);

            // Separa as validações e garante que 'required' venha primeiro
            $validations = $this->orderValidations($validation);

            // Executa as validações na ordem correta
            foreach ($validations as $validationRule) {
                if (method_exists($this, $validationRule)) {
                    $this->$validationRule($field);
                }
            }
        }

        return $this->sanitize();
    }

    /**
     * Sort validations
     * */
    private function orderValidations($validation)
    {
        // Se há apenas uma validação
        if ($this->hasOneValidation($validation)) {
            return [$validation];
        }

        // Se há múltiplas validações
        if ($this->hasTwoOrMoreValidation($validation)) {
            $validations = explode(':', $validation);

            // Remove strings vazias que podem aparecer após processamento de parâmetros
            $validations = array_filter($validations, function ($val) {
                return !empty(trim($val));
            });

            // Separa 'required' das outras validações
            $requiredIndex = array_search('required', $validations);
            $orderedValidations = [];

            // Se 'required' existe, coloca primeiro
            if ($requiredIndex !== false) {
                $orderedValidations[] = 'required';
                unset($validations[$requiredIndex]);
            }

            // Adiciona as outras validações na sequência
            $orderedValidations = array_merge($orderedValidations, array_values($validations));

            return $orderedValidations;
        }

        return [];
    }

    private function validateWithParameter($field, $validation)
    {
        $validations = [];

        if (substr_count($validation, '@') > 0) {
            $validations = explode(':', $validation);
        }

        foreach ($validations as $key => $value) {
            if (substr_count($value, '@') > 0) {
                list($validationWithParameter, $parameter) = explode('@', $value);

                $this->$validationWithParameter($field, $parameter);

                unset($validations[$key]);

                $validation = implode(':', $validations);
            }
        }

        return $validation;
    }

    private function hasOneValidation($validate)
    {
        return substr_count($validate, ':') == 0;
    }

    private function hasTwoOrMoreValidation($validate)
    {
        return substr_count($validate, ':') >= 1;
    }
}
