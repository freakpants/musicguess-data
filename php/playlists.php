<?php
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT COUNT(songs_in_playlist.relation_id) AS track_count, `id`,`name`,`public`,`description` FROM playlists LEFT JOIN songs_in_playlist ON songs_in_playlist.playlist_id = playlists.id GROUP BY playlists.id ORDER BY playlists.name ASC";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

/* $id_array = array();
foreach($results as $list){
    $id_array[$list['id']] = $list;
}

echo(json_encode($id_array));
*/

echo(json_encode($results));

?>