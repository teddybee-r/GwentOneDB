<?php
declare(strict_types=1);

namespace G1\Tests;

use \PDO;
use \Exception;

Class GwentDatabase
{
    private $pdo;
    private $schema;

    private $versions;
    private $locales;


    function __construct()
    {
        include DOC_ROOT.'/config/database.php';
        try {
            $this->pdo = new PDO("pgsql:host='".$db['host']."';dbname='".$db['name']."'", $db['user'], $db['pass']);
        } catch(\Exception $e) {
            echo "Test failed >< Connect to the database: '".$db['name']."', user: '".$db['user']."', host: '".$db['host']."'\n";
            exit;
        }
        $this->schema   = $db['schema'];
        $this->locales  = $locales;
        $this->versions = $versions;
    }


    function executeTests(): void
    {
        $this->rowCount();
        $this->localeCount();
        $this->versionCount();
        $this->checkLastVersion();
    }


    public function rowCount(): void
    {
        $sql ="SELECT (SELECT COUNT(*) FROM $this->schema.data) AS data,";

        foreach($this->locales as $locale => $json_locale) {
            $sql .="(SELECT COUNT(*) FROM $this->schema.locale_$locale) AS $locale, ";
        }
        $sql = rtrim($sql, ", ");

        $stmt = $this->pdo->query($sql);
        $cards = $stmt->fetch(PDO::FETCH_ASSOC);
        $flip = array_flip($cards);
        /**
         * Flip the array (key => value ::> value => key)
         * which shrinks it to one if the values are all the same
         */
        if(array_key_exists("0", $flip)) {
            echo "Test failed >< Tables have the same number of entries: Tables are empty\n";
        } elseif(count($flip) === 1 && !array_key_exists("0", $flip)) {
            echo "Test passed :: Tables have the same number of entries: ".$cards['data']."\n";
        } else {
            echo "Test failed >< Tables have the same number of entries\n";
        }
    }


    public function versionCount(): void
    {
        $sql ="SELECT count(DISTINCT version) as versions FROM $this->schema.data";
        $stmt = $this->pdo->query($sql);
        $cards = $stmt->fetch(PDO::FETCH_ASSOC);

        if(count($this->versions) === ((int) $cards['versions'])) {
            echo "Test passed :: Tables match the number of versions configured: ".count($this->versions)."\n";
        } else {
            echo "Test failed >< Tables match the number of versions configured\n";
        }
    }


    public function localeCount(): void
    {
        $sql ="SELECT count(*) as locales FROM information_schema.tables WHERE table_schema = '$this->schema'";
        $stmt = $this->pdo->query($sql);
        $cards = $stmt->fetch(PDO::FETCH_ASSOC);

        if(count($this->locales) === ($cards['locales'] - 2)) {
            echo "Test passed :: Tables match the number of locales configured: ".count($this->locales)."\n";
        } else {
            echo "Test failed >< Tables match the number of locales configured\n";
        }
    }


    public function checkLastVersion(): void
    {
        $sql ="SELECT version FROM $this->schema.data ORDER BY i DESC LIMIT 1";
        $stmt = $this->pdo->query($sql);
        $cards = $stmt->fetch(PDO::FETCH_ASSOC);

        if(($cards['version'] ?? null) === end($this->versions)) {
            echo "Test passed :: The last entry matches the latest version (".end($this->versions).")\n";
        } else {
            echo "Test failed >< The last entry matches the latest version (".end($this->versions).")\n";
        }
    }
}