<?php

namespace App\Services;

/**
 * Marks a SQL expression as trusted (defined in code, never coming from user input).
 * Instances are passed straight into the SELECT list without any transformation.
 *
 * Create them via QueryBuilderService::raw() so callers don't need to import this class.
 */
final class RawExpression
{
    public function __construct(private string $expression) {}

    public function __toString(): string
    {
        return $this->expression;
    }
}
