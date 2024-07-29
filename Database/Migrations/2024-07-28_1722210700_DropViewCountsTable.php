<?php

namespace Database\Migrations;

use Database\Migrator\SchemaMigration;

class DropViewCountsTable implements SchemaMigration {
    public function up(): array {
        // マイグレーションロジックをここに追加
        return [
            "DROP TABLE view_counts",
            "ALTER TABLE images ADD COLUMN view_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER delete_key"
        ];
    }

    public function down(): array {
        // ロールバックロジックを追加
        return [
            "CREATE TABLE view_counts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                image_id INT,
                count INT UNSIGNED NOT NULL DEFAULT 0,
                FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
            )",
            "ALTER TABLE images DROP COLUMN view_count"
        ];
    }
}
