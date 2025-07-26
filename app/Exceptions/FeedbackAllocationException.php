<?php

namespace App\Exceptions;

use Exception;

class FeedbackAllocationException extends Exception
{
    protected string $translationKey;
    protected array $context;

    public function __construct(string $translationKey, array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($translationKey, $code, $previous);
        $this->translationKey = $translationKey;
        $this->context = $context;
    }

    public function translationKey(): string
    {
        return $this->translationKey;
    }

    public function context(): array
    {
        return $this->context;
    }
}
