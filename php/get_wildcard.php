<?php

require("connect.php");



global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);


$sql = "SELECT 'itunes' as service, id, artistName as artist, trackName as title, previewUrl as preview_url, collectionName as album, collectionViewUrl as buy_link, collectionId, artworkUrl100
 FROM itunes_tracks WHERE 1 ORDER BY RAND() LIMIT 100";

$sth = $dbh->prepare($sql);
$sth->execute();

$wildcards = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach($wildcards as $key => $wildcard){
    $new_wild = $wildcard;
    $new_wild['album_art'] =  preg_replace("/100x100bb\.jpg/","1000x1000bb.jpg",$wildcard['artworkUrl100']);
    $wildcards[$key] = $new_wild;
}

echo(json_encode($wildcards));


?>