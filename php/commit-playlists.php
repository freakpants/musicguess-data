<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

$formData = json_decode(file_get_contents('php://input'));
$playlist_ids = $formData->playlist_ids;

require("connect.php");
global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT `playlist_id`, `relation_id` FROM songs_in_playlist WHERE `track_id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $formData->track_id));
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($results as $relation){
    if(in_array($relation['playlist_id'], $playlist_ids)){
        // remove from array
        foreach($playlist_ids as $key => $value){
            if($value === $relation['playlist_id']){
                unset($playlist_ids[$key]);
            } 
        }
    } else {
        // delete in db
        $sql = "DELETE FROM songs_in_playlist WHERE relation_id = :relation_id";
        $sth = $dbh->prepare($sql);
        $sth->execute(array(
            ":relation_id" => $relation['relation_id'],
        ));
    }
}

// remaining ids need to be added to db
foreach($playlist_ids as $key => $value){
    $sql = "INSERT INTO songs_in_playlist (service, playlist_id,track_id) VALUES('itunes', :playlist_id, :track_id)";
    $sth = $dbh->prepare($sql);
    $sth->execute(array(
        ":playlist_id" => $value,
        ":track_id" => $formData->track_id
    ));
}
?>