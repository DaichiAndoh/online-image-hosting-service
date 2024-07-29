<?php

namespace Helpers;

use Database\MySQLWrapper;
use Helpers\DateTimeHelper;;
use DateTime;
use Exception;

class DatabaseHelper {
    public static function createImage(string $clientIp, string $extension, string $shareKey, string $deleteKey): int {
        $db = new MySQLWrapper();

        $stmt = $db->prepare("INSERT INTO images (ip, extension, share_key, delete_key) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $clientIp, $extension, $shareKey, $deleteKey);
        $stmt->execute();

        return $db->insert_id;
    }

    public static function getImage(string $extension, string $key, bool $delete = false): array | null {
        $db = new MySQLWrapper();

        $keyColumn = $delete ? 'delete_key' : 'share_key';
        $stmt = $db->prepare("SELECT * FROM images WHERE extension = ? AND $keyColumn = ? LIMIT 1");
        $stmt->bind_param('ss', $extension, $key);
        $stmt->execute();

        $result = $stmt->get_result();
        $image = $result->fetch_assoc();

        if (!$image) return null;
        return $image;
    }

    public static function deleteImage(int $id): void {
        $db = new MySQLWrapper();

        $stmt = $db->prepare("DELETE FROM images WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    public static function updateImage(int $id): void {
        $db = new MySQLWrapper();
        $currentDateTime = DateTimeHelper::getCurrentDateTimeStr();

        $stmt = $db->prepare("UPDATE images SET last_viewed_at = ?, view_count = view_count + 1 WHERE id = ?");
        $stmt->bind_param('si', $currentDateTime, $id);
        $stmt->execute();
    }
}
