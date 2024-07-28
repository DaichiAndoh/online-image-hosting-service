<?php

namespace Database\Migrator;

interface SchemaMigration
{
    public function up(): array;
    public function down(): array;
}
