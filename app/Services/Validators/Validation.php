<?php

namespace App\Services\Validators;

use Psr\Container\ContainerInterface;

abstract class Validation extends Sanitize
{
    private $errors = [];
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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

            if (!isset($this->container)) {
                throw new \Exception("Dependency container is not available");
            }

            // Constructs the full name of the model class
            $modelClass = "App\\Models\\{$modelName}";

            // Check if the class exists
            if (!class_exists($modelClass)) {
                throw new \Exception("Model {$modelName} not found");
            }

            $model = $this->container->get($modelClass);

            // Database search
            $result = $model->findByField($field, $_POST[$field]);

            if ($result !== null && !empty($result)) {
                $this->errors[$field][] = flash($field, error("Este {$field} já está"));
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

    public function hasErrors(array $formData)
    {
        return !empty($this->errors);
    }
}
