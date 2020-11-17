<?php
require_once('variables.php');
$list_id = 1;

echo '<table>';
echo "<tr><th class=\"no\">No</th><th class=\"part\">Part</th><th class=\"name\">Name</th><th class=\"status\">Status</th></tr>";

/* Create the DB */

$pdo = new PDO("pgsql:host=$database_server", $database_user, $database_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
$sql = "
CREATE DATABASE $database_name
    WITH 
    OWNER = $database_user
    ENCODING = 'UTF8'
    LC_COLLATE = 'English_Germany.1252'
    LC_CTYPE = 'English_Germany.1252'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;
";
$pdo->exec($sql);
echo "<tr><td class=\"no\">$list_id</td><td class=\"part\">Database</td><td class=\"name\">$database_name</td><td class=\"status\">&#10003;<td><tr>";
$list_id++;


/* Create the Schema */

$pdo = new PDO("pgsql:host=$database_server;dbname=$database_name", $database_user, $database_pass);

$sql="CREATE SCHEMA $database_schema AUTHORIZATION $database_user;";
$pdo->exec($sql);

echo "<tr><td class=\"no\">$list_id</td><td class=\"part\">Schema</td><td class=\"name\">$database_schema</td><td class=\"status\">&#10003;<td><tr>";
$list_id++;

$sql ="
CREATE TABLE $database_schema.data
(
    i integer NOT NULL,
    version character varying COLLATE pg_catalog.\"default\",
    id jsonb,
    cardid integer,
    audioid integer,
    artid integer,
    attributes jsonb
)";
$pdo->exec($sql);

$sql="
ALTER TABLE $database_schema.data
    OWNER to $database_user;";
$pdo->exec($sql);

$sql="    
CREATE INDEX \"data_index\"
ON $database_schema.data USING btree
(cardid ASC NULLS LAST, version COLLATE pg_catalog.\"default\" ASC NULLS LAST, attributes ASC NULLS LAST)
TABLESPACE pg_default";
$pdo->exec($sql);

echo "<tr><td class=\"no\">$list_id</td><td class=\"part\">Table</td><td class=\"name\">$database_schema.data</td><td class=\"status\">&#10003;</td><td>";
$list_id++;

foreach ( $locales as $locale => $jsonLocale )
{
    $sql="
    CREATE TABLE $database_schema.locale_$locale
    (
        i integer NOT NULL,
        name character varying COLLATE pg_catalog.\"default\",
        category text COLLATE pg_catalog.\"default\",
        ability text COLLATE pg_catalog.\"default\",
        ability_html text COLLATE pg_catalog.\"default\",
        keyword_html text COLLATE pg_catalog.\"default\",
        flavor text COLLATE pg_catalog.\"default\"
    )";
    $pdo->exec($sql);

    $sql="
    ALTER TABLE $database_schema.locale_$locale
        OWNER to $database_user";
    $pdo->exec($sql);
    
    echo "<tr><td class=\"no\">$list_id</td><td class=\"part\">Table</td><td class=\"name\">$database_schema.locale_$locale</td><td class=\"status\">&#10003;</td></tr>";
    $list_id++;
}

echo '</table>';
echo '<style>
table .no {
    width:50px;
    text-align:right;    
}
table .part {
    width:80px;
    text-align:right;
}
table .name {
    width:120px;
    padding: 3px 10px;
}
table .status {
    width:40px;
    text-align:center;
}
</style>';