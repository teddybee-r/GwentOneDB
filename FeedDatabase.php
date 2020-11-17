<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<pre>
<?php
require_once('variables.php');

$pdo = new PDO("pgsql:host=$database_server;dbname=$database_name", $database_user, $database_pass);
$i = 1; 

foreach ($versions as $v => $location)
{
    $version = $v;
    $json    = file_get_contents($location);
    $data    = json_decode($json, true);

    foreach ($data as $key => $value)
    {
        $cardid		    = $value['cardId'];
    	$artid			= $value['ArtId'];
        $audioid		= $value['AudioId'];

        $json_id = "{ \"card\": $cardid, \"art\": $artid, \"audio\": $audioid }";

    	$name_en        = str_replace( "'", "''", $value['name']['en-US'] );
    	$name_de        = str_replace( "'", "''", $value['name']['de-DE'] );
    	$name_es        = str_replace( "'", "''", $value['name']['es-ES'] );
    	$name_mx        = str_replace( "'", "''", $value['name']['es-MX'] );
    	$name_fr        = str_replace( "'", "''", $value['name']['fr-FR'] );
    	$name_it        = str_replace( "'", "''", $value['name']['it-IT'] );
    	$name_jp        = str_replace( "'", "''", $value['name']['ja-JP'] );
    	$name_kr        = str_replace( "'", "''", $value['name']['ko-KR'] );
    	$name_pl        = str_replace( "'", "''", $value['name']['pl-PL'] );
    	$name_pt        = str_replace( "'", "''", $value['name']['pt-BR'] );
    	$name_ru        = str_replace( "'", "''", $value['name']['ru-RU'] );
    	$name_cn        = str_replace( "'", "''", $value['name']['zh-CN'] );
        $name_tw        = str_replace( "'", "''", $value['name']['zh-TW'] );

    	$power   		= $value['power'] ?? 0;
    	$armor		 	= $value['armor'] ?? 0;
        $provision 		= $value['provision'] ?? 0;
        $reach 		    = $value['reach'] ?? 0;

    	$faction 		= $value['faction'];
        $faction2 		= $value['factionSecondary'] ?? '';
    	$color 			= $value['color'];
    	$type	    	= $value['type'];
        $rarity			= $value['rarity'];
        $artist 		= $value['artist'] ?? 'N/A';
        $artist         = str_replace( "'", "''", $artist );
    	$released		= $value['released'];
    	$availability	= $value['availability'];
        $keyword        = implode(', ', $value['keywords']);				
    	$related        = implode(', ', $value['related']);

        $json_attributes = "{ \"provision\": $provision, \"power\": $power, \"armor\": $armor, \"reach\": $reach, \"type\": \"$type\", \"color\": \"$color\", \"rarity\": \"$rarity\", \"set\": \"$availability\", \"related\": \"$related\", \"artist\": \"$artist\", \"faction\": \"$faction\", \"factionSecondary\": \"$faction2\" }";

        $category_en    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['en-US'] ) );
        $category_de    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['de-DE'] ) );
        $category_es    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['es-ES'] ) );
        $category_mx    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['es-MX'] ) );
        $category_fr    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['fr-FR'] ) );
        $category_it    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['it-IT'] ) );
        $category_jp    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['ja-JP'] ) );
        $category_kr    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['ko-KR'] ) );
        $category_pl    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['pl-PL'] ) );
        $category_pt    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['pt-BR'] ) );
        $category_ru    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['ru-RU'] ) );
        $category_cn    = str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['categories']['zh-CN'] ) );

        $ability_en 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['en-US'] ) );
        $ability_de 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['de-DE'] ) );
        $ability_es 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['es-ES'] ) );
        $ability_mx 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['es-MX'] ) );
        $ability_fr 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['fr-FR'] ) );
        $ability_it 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['it-IT'] ) );
        $ability_jp 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['ja-JP'] ) );
        $ability_kr 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['ko-KR'] ) );
        $ability_pl 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['pl-PL'] ) );
        $ability_pt 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['pt-BR'] ) );
        $ability_ru 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['ru-RU'] ) );
        $ability_cn 		= str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['ability']['zh-CN'] ) );

        $abilityHTML_en		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['en-US'] ) );
        $abilityHTML_de		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['de-DE'] ) );
        $abilityHTML_es		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['es-ES'] ) );
        $abilityHTML_mx		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['es-MX'] ) );
        $abilityHTML_fr		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['fr-FR'] ) );
        $abilityHTML_it		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['it-IT'] ) );
        $abilityHTML_jp		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['ja-JP'] ) );
        $abilityHTML_kr		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['ko-KR'] ) );
        $abilityHTML_pl		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['pl-PL'] ) );
        $abilityHTML_pt		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['pt-BR'] ) );
        $abilityHTML_ru		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['ru-RU'] ) );
        $abilityHTML_cn		=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['abilityHTML']['zh-CN'] ) );

        $keywordHTML_en	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['en-US'] ) );
        $keywordHTML_de	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['de-DE'] ) );
        $keywordHTML_es	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['es-ES'] ) );
        $keywordHTML_mx	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['es-MX'] ) );
        $keywordHTML_fr	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['fr-FR'] ) );
        $keywordHTML_it	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['it-IT'] ) );
        $keywordHTML_jp	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['ja-JP'] ) );
        $keywordHTML_kr	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['ko-KR'] ) );
        $keywordHTML_pl	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['pl-PL'] ) );
        $keywordHTML_pt	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['pt-BR'] ) );
        $keywordHTML_ru	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['ru-RU'] ) );
        $keywordHTML_cn	=  str_replace( array("'", "\""), "''", str_replace( "\n", "\\n", $value['keywordsHTML']['zh-CN'] ) );


        $flavor_en          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['en-US'] ) );
        $flavor_de          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['de-DE'] ) );
        $flavor_es          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['es-ES'] ) );
        $flavor_mx          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['es-MX'] ) );
        $flavor_fr          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['fr-FR'] ) );
        $flavor_it          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['it-IT'] ) );
        $flavor_jp          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['ja-JP'] ) );
        $flavor_kr          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['ko-KR'] ) );
        $flavor_pl          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['pl-PL'] ) );
        $flavor_pt          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['pt-BR'] ) );
        $flavor_ru          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['ru-RU'] ) );
        $flavor_cn          =  str_replace( array("'", "\""), "''",  str_replace( "\n", "\\n", $value['flavor']['zh-CN'] ) );


        $json_en = "{ \"name\": \"$name_en\", \"category\": \"$category_en\", \"ability\": \"$ability_en\", \"ability_html\": \"$abilityHTML_en\", \"keyword_html\": \"$keywordHTML_en\", \"flavor\": \"$flavor_en\" }";
        $json_de = "{ \"name\": \"$name_de\", \"category\": \"$category_de\", \"ability\": \"$ability_de\", \"ability_html\": \"$abilityHTML_de\", \"keyword_html\": \"$keywordHTML_de\", \"flavor\": \"$flavor_de\" }";
        $json_es = "{ \"name\": \"$name_es\", \"category\": \"$category_es\", \"ability\": \"$ability_es\", \"ability_html\": \"$abilityHTML_es\", \"keyword_html\": \"$keywordHTML_es\", \"flavor\": \"$flavor_es\" }";
        $json_fr = "{ \"name\": \"$name_fr\", \"category\": \"$category_fr\", \"ability\": \"$ability_fr\", \"ability_html\": \"$abilityHTML_fr\", \"keyword_html\": \"$keywordHTML_fr\", \"flavor\": \"$flavor_fr\" }";
        $json_it = "{ \"name\": \"$name_it\", \"category\": \"$category_it\", \"ability\": \"$ability_it\", \"ability_html\": \"$abilityHTML_it\", \"keyword_html\": \"$keywordHTML_it\", \"flavor\": \"$flavor_it\" }";
        $json_jp = "{ \"name\": \"$name_jp\", \"category\": \"$category_jp\", \"ability\": \"$ability_jp\", \"ability_html\": \"$abilityHTML_jp\", \"keyword_html\": \"$keywordHTML_jp\", \"flavor\": \"$flavor_jp\" }";
        $json_kr = "{ \"name\": \"$name_kr\", \"category\": \"$category_kr\", \"ability\": \"$ability_kr\", \"ability_html\": \"$abilityHTML_kr\", \"keyword_html\": \"$keywordHTML_kr\", \"flavor\": \"$flavor_kr\" }";
        $json_mx = "{ \"name\": \"$name_mx\", \"category\": \"$category_mx\", \"ability\": \"$ability_mx\", \"ability_html\": \"$abilityHTML_mx\", \"keyword_html\": \"$keywordHTML_mx\", \"flavor\": \"$flavor_mx\" }";
        $json_pl = "{ \"name\": \"$name_pl\", \"category\": \"$category_pl\", \"ability\": \"$ability_pl\", \"ability_html\": \"$abilityHTML_pl\", \"keyword_html\": \"$keywordHTML_pl\", \"flavor\": \"$flavor_pl\" }";
        $json_pt = "{ \"name\": \"$name_pt\", \"category\": \"$category_pt\", \"ability\": \"$ability_pt\", \"ability_html\": \"$abilityHTML_pt\", \"keyword_html\": \"$keywordHTML_pt\", \"flavor\": \"$flavor_pt\" }";
        $json_ru = "{ \"name\": \"$name_ru\", \"category\": \"$category_ru\", \"ability\": \"$ability_ru\", \"ability_html\": \"$abilityHTML_ru\", \"keyword_html\": \"$keywordHTML_ru\", \"flavor\": \"$flavor_ru\" }";
        $json_cn = "{ \"name\": \"$name_cn\", \"category\": \"$category_cn\", \"ability\": \"$ability_cn\", \"ability_html\": \"$abilityHTML_cn\", \"keyword_html\": \"$keywordHTML_cn\", \"flavor\": \"$flavor_cn\" }";

        $sql ="
        INSERT INTO $database_schema.data (  i,   version,         id,   cardid,  audioid,   artid,         attributes )
        VALUES                ( $i, '$version', '$json_id', $cardid, $audioid, '$artid', '$json_attributes' )";

        $pdo->exec($sql);;
        foreach ( $locales as $locale )
        {
            $sql ="
            INSERT INTO $database_schema.locale_$locale
                   (  i,   name,       category,        ability,       ability_html,      keyword_html,      flavor )
            VALUES ( $i, '$name._.$locale', '$category._.$locale', '$ability._.$locale', '$abilityHTML._.$locale', '$keywordHTML._.$locale', '$flavor._.$locale' )";

            $pdo->exec($sql);;
        }
        $i++;
    }
    echo $version. ' success <br>';
}
?>	

</pre>