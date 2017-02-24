<?php

// add track to playlist
require("connect.php");

$pw = $_POST['pw'];

if( $pw !== 'Hr8q72UmMCNWc8PHm9zED9FG' ){
	die;
}

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$track_id = $_POST['track_id'];
$playlist_id = $_POST['playlist_id'];
$service = $_POST['service'];

$sql = "INSERT INTO songs_in_playlist (track_id,service,playlist_id) VALUES(:track_id,:service,:playlist_id)";

$stmt = $dbh->prepare($sql);

$stmt->execute(array(':track_id' => $track_id, ':playlist_id' => $playlist_id, ':service' => $service));

?>