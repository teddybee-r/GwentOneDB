<?php
## Database Configuration
$db = [
    'host'   => 'localhost',

    'name'   => 'gwent',
    'schema' => 'card',

    'user'   => '',
    'pass'   => '',
];

## Enable/Disable game versions
$versions = [
    '1.0.0.15', '1.0.0.15-2', '1.0.1.26', '1.1.0', '1.2.0', '1.3.0',
    '2.0.0', '2.0.1', '2.1.0', '2.2.0',
    '3.0.0', '3.0.1', '3.1.0', '3.2.0',
    '4.0.0', '4.0.3', '4.1.0',
    '5.0.0', '5.0.1', '5.1.0', '5.2.0',
    '6.1.0', '6.2.0',
    '7.0.0', '7.0.2', '7.1.0', '7.1.1', '7.2.0', '7.3.0', '7.4.1',
    '8.0.0', '8.1.0', '8.2.0', '8.3.0', '8.4.0', '8.5.0',
    '9.0.0', '9.1.0', '9.2.0', '9.3.0', '9.4.0', '9.5.0', '9.6.0', '9.6.1',
    '10.1.0', '10.2.0', '10.3.0', '10.4.0', '10.5.0', '10.6.0', '10.7.0', '10.8.0'
];
## Enable/Disable game locales
$locales = [
    'cn' => 'zh-CN', 'de' => 'de-DE', 'en' => 'en-US', 'es' => 'es-ES',
    'fr' => 'fr-FR', 'it' => 'it-IT', 'jp' => 'ja-JP', 'kr' => 'ko-KR',
    'mx' => 'es-MX', 'pl' => 'pl-PL', 'pt' => 'pt-BR', 'ru' => 'ru-RU'
];