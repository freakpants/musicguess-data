<?php
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT * FROM itunes_album_image_replacements";

$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll();

 foreach( $results as $image ){
	echo '<img width="200px" src="'.$image['image'].'" />'.$image['itunes_collection_id'].'</br>';
} 


?>