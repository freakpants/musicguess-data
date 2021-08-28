<?php
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);


$sql = "UPDATE itunes_tracks SET artistName = :artistName, trackName = :trackName, collectionName = :collectionName, checked = 1 WHERE `id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $_GET['id'], ":artistName" => $_GET['artist'], ":trackName" => $_GET['title'], ":collectionName" => $_GET['album'] ));

$count = $sth->rowCount();
print("Updated $count rows.\n");

?>