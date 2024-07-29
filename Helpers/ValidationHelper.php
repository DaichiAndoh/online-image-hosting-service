<?php

namespace Helpers;

require_once(sprintf("%s/../Constants/FileConstants.php", __DIR__));

class ValidationHelper {
    public static function imageType(string $type): bool {
        $allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];
        return in_array($type, $allowedTypes);
    }

    public static function fileSize(int $size, int $min = DEFAULT_FILE_MIN_SIZE, int $max = DEFAULT_FILE_MAX_SIZE): bool {
        return $size >= $min && $size <= $max;
    }
}
