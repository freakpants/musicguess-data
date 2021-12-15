<?php

require("connect.php");



global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);


$sql = "INSERT INTO `events` ( `event_type`, `playlist_id`, `devices`) VALUES ('playlist_started', :playlist_id, :devices)";

$sth = $dbh->prepare($sql);
$sth->execute(array(":playlist_id" => $_POST['playlist_id'], ":devices" => json_encode($_POST['devices']) ));

?>