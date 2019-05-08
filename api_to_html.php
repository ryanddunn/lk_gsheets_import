<?php
// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value
function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
{
    // https://www.php.net/manual/en/function.date-diff.php
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
    $interval = date_diff($datetime1, $datetime2);
    return $interval->format($differenceFormat);
}
function convertDate($s)
{
    $dateformat = strtotime($s);
    $dateformat = date('Y-m-d',$dateformat);
    return $dateformat;
}
function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();
    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "rdunn@nctconline.org:Louisburg88");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

$board_id = $_GET['bid'];
$data = CallAPI("GET", "https://nctc.leankit.com/kanban/api/boards/".$board_id);
$character = json_decode($data);
$card_lanes = $character->ReplyData[0]->Lanes;

echo "<html><body><table>";
echo "<tr>
	<td>Lane</td>
	<td>CardID</td>
	<td>CardTitle</td>
	<td>CardLastMoved</td>
	<td>CardAge</td>
	<td>Size</td>
	<td>AssignedUser</td>
	<td>Rank</td>
	<td>Tags</td>
</tr>";
foreach ($card_lanes as $lane) {
	//echo $lane->Title."\n";
	$lane_name = $lane->Title;
	$card_array = $lane->Cards;
	foreach ($card_array as $card) {
		echo "<tr>";
		echo "<td>".$lane_name."</td>";
	   	echo "<td>".$card->Id."</td>";
	    	echo "<td>".$card->Title."</td>";
	    	echo "<td>".convertDate($card->LastMove)."</td>";
	    	echo "<td>".dateDifference(convertDate($card->LastMove),date('Y-m-d'))."</td>";
	    	echo "<td>".$card->Size."</td>";
	    	echo "<td>".$card->AssignedUserId."</td>";
		echo "<td>".$card->Index."</td>";
	    	echo "<td>".$card->Tags."</td>";

// link to card ... https://nctc.leankit.com/card/820684943
	    	//echo " --- \n";
	}
	
}

echo "</table></body></html>";

/**
foreach ($card_array as $card) {
    echo "Card Id: ".$card->Id."\n";
    echo "Card Title: ".$card->Title."\n";
    //echo "Last Moved: ".$card->LastMove."\n";
    echo "Last Moved: ".convertDate($card->LastMove)."\n";
    echo "Age: ".dateDifference(convertDate($card->LastMove),date('Y-m-d'))."\n";
    echo "Size: ".$card->Size."\n";
    echo "Assigned User: ".$card->AssignedUserId."\n";
    // link to card ... https://nctc.leankit.com/card/820684943
    echo " --- \n";
}
*/
echo "\n **** \n";
?>
