<?php
/*
 * Setup your postgres configuration
 */
$database_server    = 'localhost';
$database_name      = 'gwent';
$database_user      = '';
$database_pass      = '';
$database_schema    = 'card';

$locales = ['cn' => 'zh-CN', 'de' => 'de-DE', 'en' => 'en-US', 'es' => 'es-ES',
            'fr' => 'fr-FR', 'it' => 'it-IT', 'jp' => 'ja-JP', 'kr' => 'ko-KR',
            'mx' => 'es-MX', 'pl' => 'pl-PL', 'pt' => 'pt-BR', 'ru' => 'ru-RU'];

/* the script takes a while due to 
 * almost 30 versions of the game 
 * being fed into the Database
 */
ini_set("max_execution_time", 0);

/*
 * This will require updates for new versions.
 */
$versions = [
  '1.0.0.15' => 'data\cards_v1.0.0.15.json',
'1.0.0.15-2' => 'data\cards_v1.0.0.15-2.json',
  '1.0.1.26' => 'data\cards_v1.0.1.26.json',
     '1.1.0' => 'data\cards_v1.1.0.json',
     '1.2.0' => 'data\cards_v1.2.0.json',
     '1.3.0' => 'data\cards_v1.3.0.json',
     '2.0.0' => 'data\cards_v2.0.0.json',
     '2.0.1' => 'data\cards_v2.0.1.json',
     '2.1.0' => 'data\cards_v2.1.0.json',
     '2.2.0' => 'data\cards_v2.2.0.json',
     '3.0.0' => 'data\cards_v3.0.0.json',
     '3.0.1' => 'data\cards_v3.0.1.json',
     '3.1.0' => 'data\cards_v3.1.0.json',
     '3.2.0' => 'data\cards_v3.2.0.json',
     '4.0.0' => 'data\cards_v4.0.0.json',
     '4.0.3' => 'data\cards_v4.0.3.json',
     '4.1.0' => 'data\cards_v4.1.0.json',
     '5.0.0' => 'data\cards_v5.0.0.json',
     '5.0.1' => 'data\cards_v5.0.1.json',
     '5.1.0' => 'data\cards_v5.1.0.json',
     '5.2.0' => 'data\cards_v5.2.0.json',
     '6.1.0' => 'data\cards_v6.1.0.json',
     '6.2.0' => 'data\cards_v6.2.0.json',
     '7.0.0' => 'data\cards_v7.0.0.json',
     '7.0.2' => 'data\cards_v7.0.2.json',
     '7.1.0' => 'data\cards_v7.1.0.json',
     '7.1.1' => 'data\cards_v7.1.1.json',
     '7.2.0' => 'data\cards_v7.2.0.json',
     '7.3.0' => 'data\cards_v7.3.0.json',
     '7.4.1' => 'data\cards_v7.4.1.json'
];