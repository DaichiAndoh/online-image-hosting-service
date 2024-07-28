<?php

namespace Helpers;

use DateTime;

class DateTimeHelper {
    public static function getCurrentDateTimeStr(): string {
        $d = new DateTime();
        return $d->format('Y-m-d H:i:s');
    }
}
