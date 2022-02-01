<?php

require("connect.php");



global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);


$sql = "SELECT * FROM  `events` WHERE expanded = 0 AND id != '38140' AND id != '52204' ORDER BY id DESC LIMIT 8000";
// $sql = "SELECT * FROM  `events` WHERE expanded = 0 ORDER BY id DESC LIMIT 8000";
// $sql = "SELECT * FROM  `events` WHERE premium = 0 LIMIT 7000";

$sth = $dbh->prepare($sql);
$sth->execute();

$events = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach($events as $event){
    $decoded = json_decode($event['devices']);
/*     echo '<pre>';
    var_dump($decoded);
    echo '</pre>';  */

    $uid = $decoded[0]->uid;
    $cc = $decoded[0]->cc;
    $language = $decoded[0]->language;

    $premium = false;

    $player_count = -1;
    $nickname = '';
    foreach($decoded as $device){
        if($nickname === '' && isset($device->nickname)){
            $nickname = $device->nickname;
        }
        if($device !== ''){
            if($device->premium === "true"){
                $premium = true;
            }
            $player_count++;
        };
    }
    echo 'count: ' . $player_count . '</br>';

    $sql = "UPDATE events SET `expanded` = 1, `premium` = :premium, `uid` = :uid, `cc` = :cc, `player_count` = :player_count, `language` = :language, `nickname` = :nickname WHERE `id` = :id";
    $sth = $dbh->prepare($sql);
    $sth->execute(array(":premium" => $premium ,":uid" => $uid, ":cc" => $cc, ":id" => $event['id'], ":player_count" => $player_count, ":language" => $language, ":nickname" => $nickname)); 
}

?>