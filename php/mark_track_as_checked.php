<?php
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);


$sql = "UPDATE itunes_tracks SET checked = 1 WHERE `id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $_GET['id'] ));

$count = $sth->rowCount();
print("Updated $count rows.\n");

?>