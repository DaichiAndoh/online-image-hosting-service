<?php

namespace Helpers;

class ValidationHelper {
    public static function imageType(string $type): bool {
        $allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];
        return in_array($type, $allowedTypes);
    }

    public static function fileSize(int $size, int $min = 1, int $max = 50000): bool {
        return $size >= $min && $size <= $max;
    }
}
