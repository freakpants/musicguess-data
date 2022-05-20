<?php
/* called by cron once a minute, updates 10 tracks */
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

global $dbh_online;
$dbh_online = new PDO('mysql:host=freakpants.ch;dbname=' . $dbname_online , $user_online, $password_online);

// SELECT ALL RECORDS WHERE THE SUM OF incorrectCount and correctCount is greater than 7, and correctPercent is over 0 and under 100
// SELECT * FROM `itunes_tracks` WHERE correctPercent > 0 AND correctPercent < 100 AND correctCount + incorrectCount > 7 ORDER BY `itunes_tracks`.`averageTime` DESC

// get all online tracks that have difficulty data
$sql = "SELECT * FROM itunes_tracks WHERE correctCount > 0 OR inCorrectCount > 0";


$sth = $dbh_online->prepare($sql);
$sth->execute();
$results = $sth->fetchAll(PDO::FETCH_ASSOC);
// go over the results in a for each loop

foreach($results as $track){
    // echo the id
    echo $track['id'] . '<br>';
    echo 'Correct: ' . $track['correctCount'] . '<br>';
    echo 'Incorrect: ' . $track['incorrectCount'] . '<br>';

    // calculate average time
    $correctTime = $track['correctTime'];
    $correctCount  = $track['correctCount'];
    $incorrectCount = $track['incorrectCount'];

    $totalTime = (30 * $incorrectCount) + $correctTime;

    $averageTime = $totalTime / ($correctCount + $incorrectCount);   

    // calculate correct percent




    $correctPercent = $correctCount / ($correctCount + $incorrectCount) * 100;

    // echo the average time and correct percent
    echo 'Total Time: ' . $correctTime . '<br>';
    echo "Average Time: " . $averageTime . '<br>';
    echo "Percent Correct: " . $correctPercent . '<br>';
    echo '<br>';

    // update the online database
    $sql = "UPDATE itunes_tracks SET averageTime = :averageTime, correctPercent = :correctPercent WHERE id = :id";
    $sth = $dbh_online->prepare($sql);
    $sth->execute(
        array(
            ":id" => $track['id'],
            ":averageTime" => $averageTime,
            ":correctPercent" => $correctPercent
        )
    );

    // update the local database
    $sql = "UPDATE itunes_tracks SET correctTime = :correctTime, correctCount = :correctCount, incorrectCount = :incorrectCount, averageTime = :averageTime, correctPercent = :correctPercent WHERE id = :id";
    $sth = $dbh->prepare($sql);
    $sth->execute(
        array(
            ":id" => $track['id'],
            ":correctTime" => $correctTime,
            ":correctCount" => $correctCount,
            ":incorrectCount" => $incorrectCount,
            ":averageTime" => $averageTime,
            ":correctPercent" => $correctPercent
        )
    );
}


?>