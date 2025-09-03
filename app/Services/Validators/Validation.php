<?php

namespace App\Services\Validators;

abstract class Validation extends Sanitize
{
    private $errors = [];

    protected function required(string $field)
    {
        if (empty($_POST[$field])) {
            $this->errors[$field][] = flash($field, error('Compo obrigatório'));
        }
    }

    protected function email(string $field)
    {
        if (!filter_var($_POST[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = flash($field, error("O e-mail inserido é inválido"));
        }
    }

    protected function username(string $field)
    {
        if (preg_match('/[^A-Za-z0-9\.@_-]/', $_POST[$field])) {
            $this->errors[$field][] = flash($field, error('O nome de usuário pode conter apenas caracteres minúsculos alfanuméricos (letras e números), sublinhado (_), hífen (-), ponto (.) ou símbolo arroba (@).'));
        }
    }

    protected function max(string $field, int $max)
    {
        if (strlen($_POST[$field]) > $max) {
            $this->errors[$field][] = flash($field, error("O número de caracteres para este campo não pode ser maior que {$max}"));
        }
    }

    protected function unique(string $field, string $modelName)
    {
        if (empty($_POST[$field])) {
            return; // If empty, let the 'required' validation handle it
        }

        try {

            global $container;

            if (!isset($container)) {
                throw new \Exception("Dependency container is not available");
            }

            // Constructs the full name of the model class
            $modelClass = "App\\Models\\{$modelName}";

            // Check if the class exists
            if (!class_exists($modelClass)) {
                throw new \Exception("Modelo {$modelName} não encontrado");
            }

            // Get the model through the container
            $model = $container->get($modelClass);

            // Determine which method to use based on the field
            $searchMethod = $this->getSearchMethodForField($field);

            // Check if the method exists in the model
            if (!method_exists($model, $searchMethod)) {
                throw new \Exception("Método {$searchMethod} não encontrado no modelo {$modelName}");
            }

            // Database search
            $result = $model->$searchMethod($_POST[$field]);
            dd($result);
            // Se encontrou um registro, significa que não é único
            if ($result !== null && !empty($result)) {
                $this->errors[$field][] = flash($field, error("Este {$this->getFieldLabel($field)} já está sendo usado"));
            }
        } catch (\Exception $e) {
            // Em caso de erro, registra o erro de validação
            $this->errors[$field][] = flash($field, error("Erro ao validar unicidade: " . $e->getMessage()));
            throw new \Error($e->getMessage());
        }
    }

    protected function uppercase(string $field)
    {
        $_POST[$field] = mb_strtoupper($_POST[$field]);
    }

    private function getSearchMethodForField(string $field): string
    {
        switch ($field) {
            case 'email':
                return 'findByEmail';
            case 'id':
                return 'findById';
            case 'username':
                return 'findByUsername';
            case 'cpf':
                return 'findByCpf';
            case 'cnpj':
                return 'findByCnpj';
            default:
                // Para outros campos, assume um método padrão findBy + CampoCapitalizado
                return 'findBy' . ucfirst($field);
        }
    }


    private function hasErrors(array $formData = [])
    {
        return !empty($this->errors);
    }
}
