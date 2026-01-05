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
        if (empty($_POST[$field]) || !isset($_POST[$field]) || $_POST[$field] === '') {
            $this->errors[$field][] = flash($field, error('Compo obrigatório'));
        } else if (trim($_POST[$field]) === '') {
            $this->errors[$field][] = flash($field, error('O campo contém apenas espaços'));
        }
    }

    protected function email(string $field)
    {
        if (!empty($_POST[$field]) && isset($_POST[$field])) {
            if (!filter_var($_POST[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field][] = flash($field, error("O e-mail inserido é inválido"));
            }
        }
    }

    protected function max(string $field, int $max)
    {
        if (strlen($_POST[$field]) > $max) {
            $this->errors[$field][] = flash($field, error("O número de caracteres para este campo não pode ser maior que {$max}"));
        }
    }

    protected function min(string $field, int $min)
    {
        if (strlen($_POST[$field]) < $min) {
            $this->errors[$field][] = flash($field, error("O número de caracteres para este campo deve ser maior ou igual a {$min}"));
        }
    }

    protected function uppercase(string $field)
    {
        $_POST[$field] = mb_strtoupper($_POST[$field]);
    }

    protected function onlyLetter(string $field)
    {
        $letterRegex = '/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\s]+$/';

        if (!preg_match($letterRegex, $_POST[$field]) && !empty($_POST[$field])) {
            $this->errors[$field][] = flash($field, error('Apenas letras são permitidas neste campo.'));
        }
    }

    public function hasErrors(array $formData): bool
    {
        return !empty($this->errors);
    }

    public function setError(string $field, string $message): void
    {
        $this->errors[$field][] = flash($field, error($message));
    }
}
