<?php

use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Helpers\DateTimeHelper;

return [
    '/' => function(string $path): HTTPRenderer {
        return new HTMLRenderer('form', []);
    },
    '/create' => function(string $path): HTTPRenderer {
        return new HTMLRenderer('create', []);
    },
    '/share' => function(string $path): HTTPRenderer{
        return new HTMLRenderer('share', []);
    },
];
