<?php

Class CreateGwentDatabase
{
	private $versions = [
		'1.0.0.15', '1.0.0.15-2', '1.0.1.26', '1.1.0', '1.2.0', '1.3.0',
		'2.0.0', '2.0.1', '2.1.0', '2.2.0',
		'3.0.0', '3.0.1', '3.1.0', '3.2.0',
		'4.0.0', '4.0.3', '4.1.0',
		'5.0.0', '5.0.1', '5.1.0', '5.2.0',
		'6.1.0', '6.2.0', 
		'7.0.0', '7.0.2', '7.1.0', '7.1.1', '7.2.0', '7.3.0', '7.4.1',
		'8.0.0', '8.1.0', '8.2.0', '8.3.0', '8.4.0', '8.5.0',
		'9.0.0', '9.1.0', '9.2.0', '9.3.0'
	];
	private $locales = [
		'cn' => 'zh-CN', 'de' => 'de-DE', 'en' => 'en-US', 'es' => 'es-ES',
		'fr' => 'fr-FR', 'it' => 'it-IT', 'jp' => 'ja-JP', 'kr' => 'ko-KR',
		'mx' => 'es-MX', 'pl' => 'pl-PL', 'pt' => 'pt-BR', 'ru' => 'ru-RU'
	];

	/*
	 * 1. Create DB
	 * 2. Create Schema
	 * 3. Create Table data
	 * 4. Create Table changelog
	 * 5+ Create Tables locale_cn/de/en/..
	 */

	public function createDBStructure()
	{

		$pdo = new PDO("pgsql:host=".DB_HOST."", DB_USER, DB_PASS);

		/* * * * *  D A T A B A S E  * * * * */

		$sql = "
			CREATE DATABASE ".DB_NAME."
			WITH 
			OWNER = ".DB_USER."
			ENCODING = 'UTF8'
			TABLESPACE = pg_default
			CONNECTION LIMIT = -1
			TEMPLATE template0;
		";
		$pdo->exec($sql);
		echo "=> Database: ".DB_NAME." \n";

		/* Add dbname to PDO */
		$pdo = new PDO("pgsql:host=".DB_HOST.";dbname=".DB_NAME."", DB_USER, DB_PASS);
		
		/* * * * *  S C H E M A  * * * * */

		$sql="CREATE SCHEMA ".DB_SCHEMA." AUTHORIZATION ".DB_USER.";";
		$pdo->exec($sql);

		echo "=> Schema: ".DB_SCHEMA." \n";

		/* * * * *  T A B L E S  * * * * */

		/* * * * *  D A T A  * * * * */

		$sql ="
		CREATE TABLE IF NOT EXISTS ".DB_SCHEMA.".data
		(
			i SERIAL PRIMARY KEY,
			version character varying COLLATE pg_catalog.\"default\",
			id jsonb,
			attributes jsonb,
			audiofiles jsonb
		);
		ALTER TABLE ".DB_SCHEMA.".data
		OWNER to ".DB_USER."";
		$pdo->exec($sql);
		echo "=> Table: ".DB_SCHEMA.".data \n";
		
		/* * * * *  C H A N G L O G  * * * * */

		$sql ="
		CREATE TABLE IF NOT EXISTS ".DB_SCHEMA.".changelog
		(
		    i SERIAL PRIMARY KEY,
		    version character varying COLLATE pg_catalog.\"default\",
		    card int,
		    type character varying COLLATE pg_catalog.\"default\",
		    change jsonb
		);
		ALTER TABLE ".DB_SCHEMA.".changelog
		OWNER to ".DB_USER."";
		$pdo->exec($sql);
		echo "=> Table: ".DB_SCHEMA.".changelog \n";

		/* * * * *  L O C A L E S  * * * * */

		foreach ( $this->locales as $locale => $jsonLocale )
		{
		    $sql="
		    CREATE TABLE IF NOT EXISTS ".DB_SCHEMA.".locale_$locale
		    (
		        i SERIAL PRIMARY KEY,
		        name character varying COLLATE \"und-x-icu\",
		        category text COLLATE \"und-x-icu\",
		        ability text COLLATE \"und-x-icu\",
		        ability_html text COLLATE \"und-x-icu\",
		        keyword_html text COLLATE \"und-x-icu\",
		        flavor text COLLATE \"und-x-icu\"
			);
		    ALTER TABLE ".DB_SCHEMA.".locale_$locale
		    OWNER to ".DB_USER."";
		    $pdo->exec($sql);
			echo "=> Table: ".DB_SCHEMA.".locale_$locale \n";
		}
	}


	/*
	 * 1. Insert into data & locale tables
	 * 2. Insert into changelog
	 * 
	 * If a version is specified only insert a single card version.
	 */

	public function insertData($v = '')
	{
		$pdo = new PDO("pgsql:host=".DB_HOST.";dbname=".DB_NAME."", DB_USER, DB_PASS);

		if ( $v !== '') {
			if ( in_array( $v, $this->versions ) ) {
				$versions = [ "$v" ];
			} else {
				echo "=> Error: '$v' - version not found \n";
				$versions = [];
			}
		} else {
			$versions = $this->versions;
		}
		/* Loop through every version first */
		foreach ($versions as $version)
		{
			$json    = file_get_contents("src/Database/data/cards_v$version.json");
			$data    = json_decode($json, true);
		
			/* Loop through every json field (id) */
			foreach ($data as $key => $value)
			{
				/* Loop through every locale */
				foreach ( $this->locales as $locale => $jsonLocale)
				{
					$name           = str_replace( "'", "''", $value['name'][$jsonLocale] );
					$category       = str_replace( "'", "''", $value['categories'][$jsonLocale] ) ;
					$ability        = str_replace( "'", "''", $value['ability'][$jsonLocale] ) ;
					$abilityHTML    = str_replace( "'", "''", $value['abilityHTML'][$jsonLocale] ) ;
					$keywordHTML    = str_replace( "'", "''", $value['keywordsHTML'][$jsonLocale] ) ;
					$flavor         = str_replace( "'", "''", $value['flavor'][$jsonLocale] ) ;
		
					$sql ="INSERT INTO ".DB_SCHEMA.".locale_$locale
						   ( i,         name,    category,    ability,    ability_html,   keyword_html,   flavor  )
					VALUES ( DEFAULT, '$name', '$category', '$ability', '$abilityHTML', '$keywordHTML', '$flavor' )";
					$pdo->exec($sql);
				}
				$cardid         = $value['cardId'];
				$artid          = $value['ArtId'];
				$audioid        = $value['AudioId'];
				$json_id        = "{ \"card\": $cardid, \"art\": $artid, \"audio\": $audioid }";
		
				$power          = $value['power'] ?? 0;
				$armor          = $value['armor'] ?? 0;
				$provision      = $value['provision'] ?? 0;
				$reach          = $value['reach'] ?? 0;
		
				$faction        = $value['faction'];
				$faction2       = $value['factionSecondary'] ?? '';
				$color          = $value['color'];
				$type           = $value['type'];
				$rarity         = $value['rarity'];
				$artist         = $value['artist'] ?? 'N/A';
				$artist         = str_replace( "'", "''", $artist );
				$released       = $value['released'];
				$availability   = $value['availability'];
				$keyword        = implode( ', ', $value['keywords'] );				
				$related        = implode( ', ', $value['related'] );
		
				$json_audiofiles = json_encode( $value['AudioFile'] );
				
				$json_attributes = "{ \"provision\": $provision, \"power\": $power, \"armor\": $armor, \"reach\": $reach, \"type\": \"$type\", \"color\": \"$color\", \"rarity\": \"$rarity\", \"set\": \"$availability\", \"related\": \"$related\", \"artist\": \"$artist\", \"faction\": \"$faction\", \"factionSecondary\": \"$faction2\" }";
		
				$sql ="INSERT INTO ".DB_SCHEMA.".data
					   ( i,         version,         id,         attributes,         audiofiles )
				VALUES ( DEFAULT, '$version', '$json_id', '$json_attributes', '$json_audiofiles' )";

				$pdo->exec($sql);;
			}
			echo "=> Insert Data: $version \n";
		}

		$json    = file_get_contents('src/database/data/changelog.json');
		$data    = json_decode($json, true);
		/* Loop through every json field (id) */
		
		foreach( $data as $version => $cards ) {

			for($i = 0; $i < count($cards); $i++ ) {

				$card_id	= $cards[$i]['card'];
				$change		= $cards[$i]['change'];

				$changeArray = [];
				$cards[$i]['power']				? $changeArray[] = 'power' : '';
				$cards[$i]['armor']				? $changeArray[] = 'armor' : '';
				$cards[$i]['provision']			? $changeArray[] = 'provision' : '';
				$cards[$i]['leader-provision']	? $changeArray[] = 'leader' : '';
				$cards[$i]['category']			? $changeArray[] = 'category' : '';
				$cards[$i]['ability']			? $changeArray[] = 'ability' : '';
				$cards[$i]['keyword']			? $changeArray[] = 'keyword' : '';
				$json = json_encode($changeArray);

				if ( $v === $version ) {
					$sql ="INSERT INTO ".DB_SCHEMA.".changelog
						   ( i,         card,         version,         type,         change)
					VALUES ( DEFAULT, '$card_id', '$version', '$change', '$json' )";

					$pdo->exec($sql);
				} elseif ( $v === '') {
					$sql ="INSERT INTO ".DB_SCHEMA.".changelog
						   ( i,         card,         version,         type,         change)
					VALUES ( DEFAULT, '$card_id', '$version', '$change', '$json' )";
					$pdo->exec($sql);
				}
			}
		}
		echo "=> Insert Data: Changelog";
	}
	

	public function dropDatabase()
	{
		$pdo = new PDO("pgsql:host=".DB_HOST."", DB_USER, DB_PASS);

		/* Drop active connections */
		try {
			$sql = "
			REVOKE CONNECT ON DATABASE ".DB_NAME." FROM public;

			SELECT pid, pg_terminate_backend(pid) 
			FROM pg_stat_activity 
			WHERE datname = '".DB_NAME."' AND pid <> pg_backend_pid();
			";
			$pdo->exec($sql);

			/* Drop database */
			$sql = "DROP DATABASE IF EXISTS ".DB_NAME."";
			$pdo->exec($sql);

			echo "=> Database '".DB_NAME."' dropped";
		} catch(Exception $e) {
			echo "=> Database '".DB_NAME."' not found";
		}
	}

}