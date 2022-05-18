<?php

error_reporting(E_ALL);

require("connect.php");



global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname, $user, $password);
$trackResults = $_POST['roundDifficultyResults'];

foreach ($trackResults as $track) {
    $sql = "SELECT correctCount, correctTime, incorrectCount FROM itunes_tracks WHERE id = :id";
    $sth = $dbh->prepare($sql);
    $sth->execute(array(":id" => $track['id']));
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        // try to update the counts
        $sql = "UPDATE itunes_tracks SET correctCount = :correctCount, correctTime = :correctTime, incorrectCount = :incorrectCount WHERE id = :id";
        $sth = $dbh->prepare($sql);
        $sth->execute(
            array(
                ":id" => $track['id'],
                ":correctCount" => $results[0]['correctCount'] + $track['correct'],
                ":correctTime" => $results[0]['correctTime'] + $track['correctTime'],
                ":incorrectCount" => $results[0]['incorrectCount'] + $track['incorrect']
            )
        );
    } else {
        // create a new entry
        echo 'attempting to create new track entry';
        $sql = "INSERT INTO itunes_tracks (id, correctCount, correctTime, incorrectCount) VALUES (:id, :correctCount, :correctTime, :incorrectCount)";
        $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $sth = $dbh->prepare($sql);
        $sth->execute(
            array(
                ":id" => $track['id'],
                ":correctCount" => $track['correct'],
                ":correctTime" => $track['correctTime'],
                ":incorrectCount" => $track['incorrect']
            )
        );
        print_r($sth->errorInfo());
    }
}