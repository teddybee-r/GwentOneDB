<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<pre>
<?php
    /* 
     * Legacy audio link
     * generates a csv linking audio ids to the files
     * used by index
     */
    echo "i;audioid;file<br>";
    $i = 1; 
    $file    = file_get_contents('data\cards_v8.0.0.json');
    $data    = json_decode($file, true);
    /* Loop through every json field (id) */
    foreach ($data as $key => $value)
    {
        #Shupe, Dana, Arnjo, Morvran
        $ignore = [201725, 201726, 201727, 201728, 201729, 201730, 201731, 201732, 201733, 201734, 201735, 201736, 201737, 201738, 201739, 201740, 201741, 201742,
                   202705,
                   202182,
                   202707];
        $audiofile = $value['AudioFile'];
        $audioid   = $value['AudioId'];
        $cardid    = $value['cardId'];
        if(!empty($audiofile) && !in_array($cardid, $ignore))
        {
            foreach($audiofile as $audio)
            {
                if($audio != '')
                {
                    echo $i.";".$audioid.";".$audio."<br>";
                    $i++;
                }
            }
        }
    }
?>	

</pre>