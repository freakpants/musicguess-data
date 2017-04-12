<?php
// add track to playlist
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);



$sql = "SELECT option_value FROM options WHERE option_key = 'password'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$db_pw = $stmt->fetchColumn();

$client_pw = $_POST['pw'];


if( $db_pw !== $client_pw ){
	die;
}


$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$track_id = $_POST['track_id'];
$playlist_id = $_POST['playlist_id'];
$service = $_POST['service'];
$lookup_id = $_POST['lookup_id'];

$sql = "INSERT INTO songs_in_playlist (track_id,service,playlist_id) VALUES(:track_id,:service,:playlist_id)";

$stmt = $dbh->prepare($sql);

$stmt->execute(array(':track_id' => $track_id, ':playlist_id' => $playlist_id, ':service' => $service));

// if the track came from the lookup table, update the added column with the track idate
if( $lookup_id !== ''){
	$sql = "UPDATE lookup SET added = :track_id WHERE id = :lookup_id";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(':track_id' => $track_id, ':lookup_id' => $lookup_id ));
	echo $sql;
}




?>