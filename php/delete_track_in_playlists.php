<?php
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);


$sql = "DELETE FROM songs_in_playlist WHERE `track_id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $_GET['id'] ));

$count = $sth->rowCount();
print("Deleted $count rows.\n");

?>