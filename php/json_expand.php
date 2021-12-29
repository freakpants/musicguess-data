<?php

require("connect.php");



global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);


$sql = "SELECT * FROM  `events`";

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

    $player_count = -1;
    $nickname = '';
    foreach($decoded as $device){
        if($nickname === '' && isset($device->nickname)){
            $nickname = $device->nickname;
        }
        if($device !== ''){
            $player_count++;
        };
    }
    echo 'count: ' . $player_count . '</br>';

    $sql = "UPDATE events SET `uid` = :uid, `cc` = :cc, `player_count` = :player_count, `language` = :language, `nickname` = :nickname WHERE `id` = :id";
    $sth = $dbh->prepare($sql);
    $sth->execute(array(":uid" => $uid, ":cc" => $cc, ":id" => $event['id'], ":player_count" => $player_count, ":language" => $language, ":nickname" => $nickname)); 
}

?>