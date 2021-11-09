<?php
require("connect.php");
// require("functions.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT `id`,`name`,`public`,`description` FROM playlists WHERE `id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $_GET['id']));
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

$playlist = $results;

$sql = "SELECT service, itunes_tracks.artworkUrl100 as itunesCover, playlist_id, track_id as trackId, track_id as id, relation_id, itunes_tracks.releaseDate as releaseDate, itunes_tracks.checked,  itunes_tracks.collectionId, itunes_tracks.artistName, itunes_tracks.previewUrl, itunes_tracks.trackName, itunes_tracks.collectionName FROM songs_in_playlist LEFT JOIN itunes_tracks ON songs_in_playlist.track_id = itunes_tracks.id  WHERE `playlist_id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $_GET['id']));
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

$result_array = array();

foreach($results as $key => $result){
    // $art_array = get_album_art($result['artistName'], $result['trackName'], $result['collectionId']);
    // $results[$key]['album_art'] = $art_array['album_art'];
    // $results[$key]['album_art_title'] = $art_array['title'];
    $sql = "SELECT relation_id, playlists.id as id FROM songs_in_playlist LEFT JOIN playlists ON songs_in_playlist.playlist_id = playlists.id  WHERE `track_id` = :id";
    $sth = $dbh->prepare($sql);
    $sth->execute(array(":id" => $result['trackId']));
    $inner_results = $sth->fetchAll(PDO::FETCH_ASSOC);
    // the results from itunes are objects, the results from the database are associative arrays, therefore we need to add them to our list differently
    
    $result['relation_ids'] = array();
    $result['playlist_ids'] = array();
    foreach($inner_results as $relation){
        array_push($result['relation_ids'], $relation['relation_id']);
        array_push($result['playlist_ids'], $relation['id']);
    }
    array_push($result_array, $result );
} 

$playlist['tracks'] = $result_array;

echo(json_encode($playlist));

?>