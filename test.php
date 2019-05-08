<?php
echo "test \n";
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
echo "\n **** \n";
//echo CallAPI("GET", "https://nctc.leankit.com/kanban/api/boards");
$data = CallAPI("GET", "https://nctc.leankit.com/kanban/api/boards/793475984");
echo "\n *** api returned *** \n";
//echo $data;
// Lanes[0] - Paused
// Lanes[1] - Stopped Work`
// Lanes[2] - Blocked
// Lanes[3] - Doing
$character = json_decode($data);
// echo $character->ReplyData[0]->Id."\n";
// echo $character->ReplyData[0]->Title."\n";
echo $character->ReplyData[0]->Lanes[3]->Title."\n";
$card_array = $character->ReplyData[0]->Lanes[3]->Cards;
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
echo "\n **** \n";
?>
