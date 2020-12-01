<?php

namespace Remix\DJ;

use Remix\Gear;
use Remix\DJ;
use Remix\RemixException;

class Table extends Gear
{
    protected $context;

    private function __construct(string $context)
    {
        $this->context = $context;
    }

    public function context(string $context): self
    {
        if (preg_match('/[\'\"\-\`\.\s]/', $context)) {
            $message = sprintf('Illegal table name "%s"', $context);
            throw new RemixException($message);
        }
        return new static($context);
    }

    public function exists(): bool
    {
        $result = DJ::play('SHOW TABLES LIKE :table;', [':table' => $this->context]);
        return count($result) > 0;
    }

    public function create(array $columns): bool
    {
        if (! $this->exists()) {
            if (count($columns) < 1) {
                $message = sprintf('Table "%s" must contains any column', $this->context);
                throw new RemixException($message);
            }
            $sql = sprintf('CREATE TABLE `%s` (', $this->context);
            $sql .= implode(', ', $columns);
            $sql .= ');';
            return DJ::play($sql) !== false;
        } else {
            $message = sprintf('Table "%s" is already exists', $this->context);
            throw new RemixException($message);
        }
    }

    public function drop(): bool
    {
        if ($this->exists()) {
            $sql = sprintf('DROP TABLE `%s`;', $this->context);
            return DJ::play($sql) !== false;
        } else {
            $message = sprintf('Table "%s" is not exists', $this->context);
            throw new RemixException($message);
        }
    }

    public function truncate(): bool
    {
        if ($this->exists()) {
            $sql = sprintf('TRUNCATE TABLE `%s`;', $this->context);
            return DJ::play($sql) !== false;
        } else {
            $message = sprintf('Table "%s" is not exists', $this->context);
            throw new RemixException($message);
        }
    }
    // function truncate()
}
// class Table
