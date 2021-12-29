<?php

require("connect.php");



global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$number_of_rounds = 0;
if(isset($_POST['number_of_rounds'])){
    $number_of_rounds = $_POST['number_of_rounds'];
}

$sql = "INSERT INTO `events` ( `event_type`, `playlist_id`, `number_of_rounds`, `devices`) VALUES ('playlist_started', :playlist_id, :number_of_rounds, :devices)";

$sth = $dbh->prepare($sql);
$sth->execute(array(":playlist_id" => $_POST['playlist_id'], ":number_of_rounds" =>  $number_of_rounds, ":devices" => json_encode($_POST['devices']) ));

?>