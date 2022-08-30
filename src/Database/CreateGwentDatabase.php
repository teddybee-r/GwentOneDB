<?php
declare(strict_types=1);

namespace G1\Database;

use \PDO;
use \Exception;

Class CreateGwentDatabase
{
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_schema;

    private $versions;
    private $locales;

    private $time_data;
    private $time_changelog;
    private $total_rows;

    function __construct($v = '')
    {
        include DOC_ROOT.'/config/database.php';

        $this->db_host   = $db['host'];
        $this->db_name   = $db['name'];
        $this->db_user   = $db['user'];
        $this->db_pass   = $db['pass'];
        $this->db_schema = $db['schema'];
        $this->locales = $locales;

        try {
            $this->pdo = new PDO("pgsql:host='".$db['host']."'", $db['user'], $db['pass']);
        } catch(\Exception $e) {
            echo "Error & Exit: Could not connect to Postgres! user: '".$db['user']."', host: '".$db['host']."'\n";
            echo $e->getMessage();
            exit;
        }

        if($v) {
            if(in_array($v, $versions)) {
                $this->versions = [ "$v" ];
            } else {
                echo "<> Error & Exit: '$v' - version not found \n";
                exit;
            }
        } else {
            $this->versions = $versions;
        }
    }

    public function createDatabase()
    {
        $pdo = new PDO("pgsql:host='$this->db_host'", $this->db_user, $this->db_pass);
        $sql_create_db = "
            CREATE DATABASE $this->db_name
            WITH 
            OWNER = $this->db_user
            ENCODING = 'UTF8'
            TABLESPACE = pg_default
            CONNECTION LIMIT = -1
            TEMPLATE template0;";
        $pdo->exec($sql_create_db);
        echo "\n=> Database: $this->db_name \n";
    }

    public function createTables()
    {
        $pdo = new PDO("pgsql:host='$this->db_host';dbname='$this->db_name'", $this->db_user, $this->db_pass);

        $sql_create_schema = "CREATE SCHEMA $this->db_schema AUTHORIZATION $this->db_user";

        $sql_create_table_data = "
            CREATE TABLE IF NOT EXISTS $this->db_schema.data
            (
                i SERIAL PRIMARY KEY,
                version character varying COLLATE pg_catalog.\"default\",
                id jsonb,
                attributes jsonb,
                audiofiles jsonb
            );
            ALTER TABLE $this->db_schema.data
            OWNER to $this->db_user";

        $sql_create_table_changelog = "
            CREATE TABLE IF NOT EXISTS $this->db_schema.changelog
            (
                i SERIAL PRIMARY KEY,
                version character varying COLLATE pg_catalog.\"default\",
                card int,
                type character varying COLLATE pg_catalog.\"default\",
                change jsonb
            );
            ALTER TABLE $this->db_schema.changelog
            OWNER to $this->db_user";

        $pdo->exec($sql_create_schema);
        echo "=> Schema: $this->db_schema \n";
        $pdo->exec($sql_create_table_data);
        echo "=> Table: $this->db_schema.data \n";
        $pdo->exec($sql_create_table_changelog);
        echo "=> Table: $this->db_schema.changelog \n";
        echo "=> Table: $this->db_schema.locale";

        foreach ($this->locales as $locale => $json_locale)
        {
            ## DB > Table: locale_XX
            $sql="
                CREATE TABLE IF NOT EXISTS $this->db_schema.locale_$locale
                (
                    i SERIAL PRIMARY KEY,
                    name character varying COLLATE \"und-x-icu\",
                    category text COLLATE \"und-x-icu\",
                    ability text COLLATE \"und-x-icu\",
                    ability_html text COLLATE \"und-x-icu\",
                    keyword_html text COLLATE \"und-x-icu\",
                    flavor text COLLATE \"und-x-icu\",
                    FOREIGN KEY(i) REFERENCES $this->db_schema.data(i) ON DELETE CASCADE
                );
                ALTER TABLE $this->db_schema.locale_$locale
                OWNER to $this->db_user
            ";
            $pdo->exec($sql);
            echo "_$locale ";
            
        }
        echo "\n";
    }

    public function insertCardData()
    {
        $pdo = new PDO("pgsql:host='$this->db_host';dbname='$this->db_name'", $this->db_user, $this->db_pass);

        $all_rows = 0;
        echo "\n\n";
        echo "  _|_|_|    _|  Depending on the hardware\n";
        echo "_|        _|_|  this may take a while!\n";
        echo "_|  _|_|    _|\n";
        echo "_|    _|    _|  Inserting " . count($this->versions) . " version(s)\n";
        echo "  _|_|_|    _|       with ". count($this->locales) . " locale(s)\n\n";

        echo "   Version\tCards\tTime\n";
        foreach($this->versions as $version)
        {
            $time_start = microtime(true);
            echo "=> $version\t";
            $json = file_get_contents("src/Database/data/cards_v$version.json");
            $data = json_decode($json, true);

            foreach($data as $key => $val)
            {
                $id              = "{ \"card\": ".$val['cardId'].", \"art\": ".$val['ArtId'].", \"audio\": ".$val['AudioId']." }";
                $attributes      = [];
                $attributes['power']        = $val['power'] ?? 0;
                $attributes['armor']        = $val['armor'] ?? 0;
                $attributes['provision']    = $val['provision'] ?? 0;
                $attributes['reach']        = $val['reach'] ?? 0;
                $attributes['faction']      = $val['faction'];
                $attributes['faction2']     = $val['factionSecondary'] ?? '';
                $attributes['color']        = $val['color'];
                $attributes['type']         = $val['type'];
                $attributes['rarity']       = $val['rarity'];
                $attributes['artist']       = $val['artist'] ?? 'N/A';
                $attributes['availability'] = $val['availability'];
                $attributes['related']      = implode( ', ', $val['related'] );
                $json_attributes = json_encode($attributes);
                $json_audiofiles = json_encode( $val['AudioFile'] );

                $sql = "INSERT INTO $this->db_schema.data (i, version, id, attributes, audiofiles)
                        VALUES (DEFAULT, '$version', '$id', '$json_attributes', '$json_audiofiles')";
                $pdo->exec($sql);;

                foreach($this->locales as $locale => $json_locale)
                {
                    ## escape '
                    $name        = $val['name'][$json_locale]         ? str_replace("'", "''", $val['name'][$json_locale])         : '';
                    $category    = $val['categories'][$json_locale]   ? str_replace("'", "''", $val['categories'][$json_locale])   : '';
                    $ability     = $val['ability'][$json_locale]      ? str_replace("'", "''", $val['ability'][$json_locale])      : '';
                    $abilityHTML = $val['abilityHTML'][$json_locale]  ? str_replace("'", "''", $val['abilityHTML'][$json_locale])  : '';
                    $keywordHTML = $val['keywordsHTML'][$json_locale] ? str_replace("'", "''", $val['keywordsHTML'][$json_locale]) : '';
                    $flavor      = $val['flavor'][$json_locale]       ? str_replace("'", "''", $val['flavor'][$json_locale])       : '';

                    $sql ="
                        INSERT INTO $this->db_schema.locale_$locale(i, name, category, ability, ability_html, keyword_html, flavor)
                        VALUES(DEFAULT, '$name', '$category', '$ability', '$abilityHTML', '$keywordHTML', '$flavor')";
                    $pdo->exec($sql);
                }
            }
            $total = count($data);
            $time_end = microtime(true);
            $all_rows += $total;
            echo "$total\t". round($time_end-$time_start, 2) ."s\n";
        }
        $this->total_rows = $all_rows;
    }

    public function insertChangelogData(): void
    {
        
        $pdo = new PDO("pgsql:host='$this->db_host';dbname='$this->db_name'", $this->db_user, $this->db_pass);

        echo "=> Insert Data: Changelog";
        $json    = file_get_contents('src/Database/data/changelog.json');
        $data    = json_decode($json, true);

        foreach($data as $version => $cards) {
            ## only insert a single version if specified
            if (in_array($version, $this->versions)) {
                for($i = 0; $i < count($cards); $i++ ) {

                    $card_id	= $cards[$i]['card'];
                    $change		= $cards[$i]['change'];

                    $change_array = [];
                    $cards[$i]['power']            ? $change_array[] = 'power'     : '';
                    $cards[$i]['armor']            ? $change_array[] = 'armor'     : '';
                    $cards[$i]['provision']        ? $change_array[] = 'provision' : '';
                    $cards[$i]['leader-provision'] ? $change_array[] = 'leader'    : '';
                    $cards[$i]['category']         ? $change_array[] = 'category'  : '';
                    $cards[$i]['ability']          ? $change_array[] = 'ability'   : '';
                    $cards[$i]['keyword']          ? $change_array[] = 'keyword'   : '';
                    $json = json_encode($change_array);
                
                    $sql = "INSERT INTO $this->db_schema.changelog (i, card, version, type, change)
                            VALUES (DEFAULT, '$card_id', '$version', '$change', '$json')";
                    $pdo->exec($sql);
                }
            }
        }
    }

    public function resetChangelog(): void
    {
        $pdo = new PDO("pgsql:host='$this->db_host';dbname='$this->db_name'", $this->db_user, $this->db_pass);

        $sql = "TRUNCATE $this->db_schema.changelog RESTART IDENTITY";
        $pdo->exec($sql);

        echo "=> Reset table: $this->db_schema.changelog (TRUNCATE)\n";

        $this->insertChangelogData();        
    }

    public function onlyLatestVersion(): void
    {
        $pdo = new PDO("pgsql:host='$this->db_host';dbname='$this->db_name'", $this->db_user, $this->db_pass);

        $sql ="SELECT version FROM $this->db_schema.data ORDER BY i DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $cards = $stmt->fetch(\PDO::FETCH_ASSOC);


        if(($cards['version'] ?? null) === end($this->versions)) {
            $version = end($this->versions);
            $this->versions = array_slice($this->versions, -1);

            echo "=> The current version is the latest (".$cards['version'].")\n";
            $sql = "DELETE FROM $this->db_schema.data WHERE $this->db_schema.data.version != '$version'";
            $pdo->exec($sql);
            echo "=> Deleted every version except the current\n";
        } else {
            $version = end($this->versions);
            $this->versions = array_slice($this->versions, -1);

            $this->insertCardData();
            $sql = "DELETE FROM $this->db_schema.data WHERE $this->db_schema.data.version != '$version'";
            $pdo->exec($sql);
            echo "\nDeleted every version except the latest\n";
        }

    }

    public function dropDatabase(): void
    {
        $pdo = new PDO("pgsql:host='$this->db_host'", $this->db_user, $this->db_pass);

        /* Drop active connections */
        try {
            $sql = "
                REVOKE CONNECT ON DATABASE \"$this->db_name\" FROM public;
                SELECT pid, pg_terminate_backend(pid) 
                FROM pg_stat_activity 
                WHERE datname = '$this->db_name' AND pid <> pg_backend_pid();
            ";
            $pdo->exec($sql);

            $sql = "DROP DATABASE IF EXISTS \"$this->db_name\"";
            $pdo->exec($sql);

            echo "=> Database '$this->db_name' dropped\n";
        } catch(Exception $e) {
            echo "=> Database '$this->db_name' not found\n";
        }
    }
}