<?php

namespace Database\Migrations;

use Database\Migrator\SchemaMigration;

class CreateImagesTable implements SchemaMigration {
    public function up(): array {
        // マイグレーションロジックをここに追加
        return [
            "CREATE TABLE images (
                id INT PRIMARY KEY AUTO_INCREMENT,
                ip VARCHAR(15) NULL,
                extension ENUM('png', 'jpeg', 'gif') NOT NULL,
                share_key VARCHAR(30) NOT NULL,
                delete_key VARCHAR(30) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                last_viewed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE view_counts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                image_id INT,
                count INT UNSIGNED NOT NULL DEFAULT 0,
                FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
            )"
        ];
    }

    public function down(): array {
        // ロールバックロジックを追加
        return [
            "DROP TABLE view_counts",
            "DROP TABLE images"
        ];
    }
}
