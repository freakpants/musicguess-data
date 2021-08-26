<?php
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT `id`,`name`,`public`,`description` FROM playlists WHERE `id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $_GET['id']));
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

$playlist = $results;

$sql = "SELECT service, playlist_id, track_id as id, relation_id, itunes_tracks.artistName, itunes_tracks.previewUrl, itunes_tracks.trackName, itunes_tracks.collectionName FROM songs_in_playlist LEFT JOIN itunes_tracks ON songs_in_playlist.track_id = itunes_tracks.id  WHERE `playlist_id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $_GET['id']));
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

$playlist['tracks'] = $results;

echo(json_encode($playlist));

?>