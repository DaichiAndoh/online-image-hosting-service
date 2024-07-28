<?php

namespace Helpers;

class ImageHelper {
    public static function imageTypeToExtension(string $type): string {
        return str_replace('image/', '', $type);
    }
}
