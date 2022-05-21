<?php
require("connect.php");
global $dbh_online;
$dbh_online = new PDO('mysql:host=freakpants.ch;dbname=' . $dbname_online , $user_online, $password_online);

$sql = "SELECT id, previewUrl, artistName, trackName, collectionName, artworkUrl100 FROM `itunes_tracks` WHERE wildcardBlocked = 0 AND checked != 1 ORDER BY RAND() LIMIT 1";
// select record
$sth = $dbh_online->prepare($sql);
$sth->execute();
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($results as $result){
    echo "id: " .$result['id'] . '<br>';
    echo "Artist: " . $result['artistName'] . '<br>';
    echo "Track: " . $result['trackName'] . '<br>';
    echo "Album: " . $result['collectionName'] . '<br>';
    ?>
    <audio controls className="player">
    {" "}
    <source src="<?php echo $result['previewUrl']; ?>" type="audio/mpeg" />
    </audio></br>
    <img src="<?php echo str_replace("100x100", "500x500", $result['artworkUrl100']); ?>" />
    <?php
}

?>