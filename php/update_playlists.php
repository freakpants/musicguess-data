<?php
/* called by cron once a minute, lookup 10 tracks, mark lookup as in progress */
require("connect.php");



global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);


$sql = "SELECT option_value FROM options WHERE option_key = 'playlist_update_running'";

$sth = $dbh->prepare($sql);
$sth->execute();
$result = $sth->fetch(); 

if($result[0] === '1'){
    die("playlist update in progress");
} else {
    $sql = "UPDATE options SET option_value = 1 WHERE option_key = 'playlist_update_running'";
    $sth = $dbh->prepare($sql);
    $sth->execute();

    $sql = "SELECT id FROM playlists WHERE update_needed = 1";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    
    $results = $sth->fetchAll();
    foreach( $results as $playlist ){
        $url = "http://localhost/musicguess-data/create_playlist.php?playlist_id=" . $playlist['id'];
        $response = file_get_contents($url);

        $id = $playlist['id'];
        $sql = "UPDATE playlists SET update_needed = 0 WHERE id = $id";
        $sth = $dbh->prepare($sql);
        $sth->execute();
    } 

    $sql = "UPDATE options SET option_value = 0 WHERE option_key = 'playlist_update_running'";
    $sth = $dbh->prepare($sql);
    $sth->execute();
}


?>