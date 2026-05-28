<?php
declare(strict_types=1);

namespace App\Core;

abstract class BaseMigration {
    protected string $filename;

    abstract public function up(): string|array;

    abstract public function down(): string|array;

    final public function getFilename(): string {
        return $this->filename;
    }
}
