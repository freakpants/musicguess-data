<?php
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT `id`,`name`,`public`,`description` FROM playlists";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

echo(json_encode($results));

?>