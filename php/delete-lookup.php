<?php
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "DELETE FROM lookup WHERE `id` = :id";
$sth = $dbh->prepare($sql);
$sth->execute(array(":id" => $_POST['id'] ));

$count = $sth->rowCount();
print("Deleted $count rows.\n");

?>