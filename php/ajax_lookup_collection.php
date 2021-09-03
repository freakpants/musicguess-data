<?php
// add track to playlist
require("connect.php");
require("itunes_functions.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);



/* $sql = "SELECT option_value FROM options WHERE option_key = 'password'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$db_pw = $stmt->fetchColumn();

$client_pw = $_POST['pw'];

if( $db_pw !== $client_pw ){
	die;
} */


$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$collection_id = $_POST['collection_id'];

lookup_collection( $collection_id );

?>