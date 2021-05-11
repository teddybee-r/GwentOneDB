<?php

/* Load requirements */
require_once('config/db-config.php');
require_once('src/Database/CreateGwentDatabase.php');


echo "\nPostgreSQL Gwent Database - gwent.one\n\n";

echo "1. Build database (".DB_NAME.") structure\n";
echo "2. Insert data: Every version\n";
echo "3. Insert data: Single version\n";
echo "4. Build database structure and insert data\n";
echo "0. Drop Database\n\n";

$input = (int)readline('Enter one of the above numbers: ');


$DB = New CreateGwentDatabase();

if ( $input === 1 ) {
	$DB->createDBStructure();
} elseif ( $input === 2 ) {
	$DB->insertData();
} elseif ( $input === 3 ) {
	$v = (string)readline("Enter the version (8.5.0): ");
	$DB->insertData($v);
} elseif ( $input === 4 ) {
	$DB->createDBStructure();
	$DB->insertData();
} elseif ( $input === 0 ) {
	$confirm = (string)readline("Type '".DB_NAME."' to confirm: ");
	if ( $confirm === DB_NAME )
	$DB->dropDatabase();
}