<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Throwable;

readonly class ConnectorException implements Throwable
{
    protected string $file;

    protected int $line;

    protected array $trace;

    public function __construct(
        protected string $message,
        protected int $code,
        protected ?Throwable $previous,
    ) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

        $this->file = $backtrace[0]['file'];
        $this->line = $backtrace[0]['line'];
        $this->trace = debug_backtrace();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getTrace(): array
    {
        return $this->trace;
    }

    public function getTraceAsString(): string
    {
        $traceString = '';
        foreach ($this->trace as $key => $frame) {
            $traceString .= "#$key {$frame['file']}({$frame['line']}): ";
            if (isset($frame['class'])) {
                $traceString .= "{$frame['class']}{$frame['type']}";
            }
            $traceString .= "{$frame['function']}()\n";
        }
        return $traceString;
    }

    public function getPrevious(): ?Throwable
    {
        return $this->previous;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s] %s in %s on line %d',
            $this->getCode(),
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
        );
    }
}
