<?php
/* called by cron once a minute, lookup 10 tracks, mark lookup as in progress */
require("connect.php");



global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

if($_GET['list']){
    // return all looked up titles
    $sql = "SELECT id, artist, title FROM lookup WHERE added = 1";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $results = $sth->fetchAll();
    echo(json_encode($results));
} else {
    // perform the actual lookup
    $sql = "SELECT option_value FROM options WHERE option_key = 'lookup_running'";

    $sth = $dbh->prepare($sql);
    $sth->execute();
    $result = $sth->fetch();

    if($result[0] === '1'){
        die("lookup in progress");
    } else {
        $sql = "UPDATE options SET option_value = 1 WHERE option_key = 'lookup_running'";
        $sth = $dbh->prepare($sql);
        $sth->execute();
        
        $sql = "SELECT * FROM lookup WHERE added = 0 ORDER BY id DESC LIMIT 10";
        $sth = $dbh->prepare($sql);
        $sth->execute();
        $lookups = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach($lookups as $track){
            $url = "http://localhost/musicguess-data/search.php?artist=" . urlencode($track['artist']) . "&title=" . urlencode($track['title']) . '&album=&searchMode=live';
            $response = file_get_contents($url);
            echo '<pre>';
            echo($response);
            echo '</pre>';

            $track_id = $track['id'];
            $sql = "UPDATE lookup SET added = 1 WHERE id = $track_id";
            $sth = $dbh->prepare($sql);
            $sth->execute();

        }

        $sql = "UPDATE options SET option_value = 0 WHERE option_key = 'lookup_running'";
        $sth = $dbh->prepare($sql);
        $sth->execute();
    }
}
?>