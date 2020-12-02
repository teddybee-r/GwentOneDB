<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<pre>
<?php
require_once('variables.php');

$pdo = new PDO("pgsql:host=$database_server;dbname=$database_name", $database_user, $database_pass);
$i = 1; 

/* Loop through every version first */
foreach ($versions as $version => $location)
{
    $json    = file_get_contents($location);
    $data    = json_decode($json, true);

    /* Loop through every json field (id) */
    foreach ($data as $key => $value)
    {
        
        /* Loop through every locale */
        foreach ( $locales as $locale => $jsonLocale)
        {
    	    $name           = str_replace( "'", "''", $value['name'][$jsonLocale] );
            $category       = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories'][$jsonLocale] ) );
            $ability        = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability'][$jsonLocale] ) );
            $abilityHTML    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML'][$jsonLocale] ) );
            $keywordHTML    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML'][$jsonLocale] ) );
            $flavor         = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['flavor'][$jsonLocale] ) );

            $sql ="INSERT INTO $database_schema.locale_$locale
                   (  i,   name,    category,    ability,    ability_html,   keyword_html,   flavor  )
            VALUES ( $i, '$name', '$category', '$ability', '$abilityHTML', '$keywordHTML', '$flavor' )";
            $pdo->exec($sql);;
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
        $keyword        = implode(', ', $value['keywords']);				
        $related        = implode(', ', $value['related']);

        $json_audiofiles = json_encode($value['AudioFile'], JSON_FORCE_OBJECT);
        
        $json_attributes = "{ \"provision\": $provision, \"power\": $power, \"armor\": $armor, \"reach\": $reach, \"type\": \"$type\", \"color\": \"$color\", \"rarity\": \"$rarity\", \"set\": \"$availability\", \"related\": \"$related\", \"artist\": \"$artist\", \"faction\": \"$faction\", \"factionSecondary\": \"$faction2\" }";

        $sql ="INSERT INTO $database_schema.data
               (  i,   version,         id,   cardid,  audioid,   artid,         attributes,         audiofiles)
        VALUES ( $i, '$version', '$json_id', $cardid, $audioid, '$artid', '$json_attributes', '$json_audiofiles' )";

        $pdo->exec($sql);;
        $i++;
    }
    echo $version. ' success <br>';
}
?>	

</pre>