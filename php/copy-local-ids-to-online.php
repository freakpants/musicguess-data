<?php
/* called by cron once a minute, updates 10 tracks */
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

global $dbh_online;
$dbh_online = new PDO('mysql:host=freakpants.ch;dbname=' . $dbname_online , $user_online, $password_online);

// get all local track ids
$sql = "SELECT `id` FROM itunes_tracks WHERE 1";


$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll(PDO::FETCH_ASSOC);
// go over the results in a for each loop

foreach($results as $track){
    // echo the id
    echo $track['id'] . '<br>';

    // insert the online id
    $sql = "INSERT INTO itunes_tracks (id) VALUES (:id)";
    
    $dbh_online->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

    $sth = $dbh_online->prepare($sql);
    $sth->bindParam(':id', $track['id']);
    $sth->execute();
    
    print_r($sth->errorInfo());
}


?>