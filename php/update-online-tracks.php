<?php
/* called by cron once a minute, updates 500 tracks */
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

global $dbh_online;
$dbh_online = new PDO('mysql:host=freakpants.ch;dbname=' . $dbname_online , $user_online, $password_online);

// get all online tracks with missing data
$sql = "SELECT * FROM itunes_tracks WHERE artistName = '' OR trackName= '' OR collectionName = '' LIMIT 500";


$sth = $dbh_online->prepare($sql);
$sth->execute();
$results = $sth->fetchAll(PDO::FETCH_ASSOC);
// go over the results in a for each loop

foreach($results as $track){
    // echo the id
    echo $track['id'] . '<br>';

    // get the data locally
    $sql = "SELECT * FROM itunes_tracks WHERE id = :id";
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $track['id']);
    $sth->execute();
    $result = $sth->fetch(PDO::FETCH_ASSOC);

    print_r($result);

    // update the online track
    $sql = "UPDATE itunes_tracks SET artistName = :artistName, trackName = :trackName, previewUrl = :previewUrl, wrapperType = :wrapperType,
    kind = :kind, artistId = :artistId, collectionId = :collectionId, collectionName = :collectionName, collectionCensoredName = :collectionCensoredName,
    trackCensoredName = :trackCensoredName, artistViewUrl = :artistViewUrl, collectionViewUrl = :collectionViewUrl, trackViewUrl = :trackViewUrl,
    artworkUrl30 = :artworkUrl30, artworkUrl60 = :artworkUrl60, artworkUrl100 = :artworkUrl100, collectionPrice = :collectionPrice, trackPrice = :trackPrice,
    releaseDate = :releaseDate, collectionExplicitness = :collectionExplicitness, trackExplicitness = :trackExplicitness, discCount = :discCount, discNumber = :discNumber,
    trackCount = :trackCount, trackNumber = :trackNumber, trackTimeMillis = :trackTimeMillis, country = :country, currency = :currency, primaryGenreName = :primaryGenreName,
    isStreamable = :isStreamable, checked = :checked WHERE id = :id";
    
    $dbh_online->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

    $sth = $dbh_online->prepare($sql);
    $sth->bindParam(':id', $track['id']);
    $sth->bindParam(':artistName', $result['artistName']);
    $sth->bindParam(':trackName', $result['trackName']);
    $sth->bindParam(':previewUrl', $result['previewUrl']);
    $sth->bindParam(':wrapperType', $result['wrapperType']);
    $sth->bindParam(':kind', $result['kind']);
    $sth->bindParam(':artistId', $result['artistId']);
    $sth->bindParam(':collectionId', $result['collectionId']);
    $sth->bindParam(':collectionName', $result['collectionName']);
    $sth->bindParam(':collectionCensoredName', $result['collectionCensoredName']);
    $sth->bindParam(':trackCensoredName', $result['trackCensoredName']);
    $sth->bindParam(':artistViewUrl', $result['artistViewUrl']);
    $sth->bindParam(':collectionViewUrl', $result['collectionViewUrl']);
    $sth->bindParam(':trackViewUrl', $result['trackViewUrl']);
    $sth->bindParam(':artworkUrl30', $result['artworkUrl30']);
    $sth->bindParam(':artworkUrl60', $result['artworkUrl60']);
    $sth->bindParam(':artworkUrl100', $result['artworkUrl100']);
    $sth->bindParam(':collectionPrice', $result['collectionPrice']);
    $sth->bindParam(':trackPrice', $result['trackPrice']);
    $sth->bindParam(':releaseDate', $result['releaseDate']);
    $sth->bindParam(':collectionExplicitness', $result['collectionExplicitness']);
    $sth->bindParam(':trackExplicitness', $result['trackExplicitness']);
    $sth->bindParam(':discCount', $result['discCount']);
    $sth->bindParam(':discNumber', $result['discNumber']);
    $sth->bindParam(':trackCount', $result['trackCount']);
    $sth->bindParam(':trackNumber', $result['trackNumber']);
    $sth->bindParam(':trackTimeMillis', $result['trackTimeMillis']);
    $sth->bindParam(':country', $result['country']);
    $sth->bindParam(':currency', $result['currency']);
    $sth->bindParam(':primaryGenreName', $result['primaryGenreName']);
    $sth->bindParam(':isStreamable', $result['isStreamable']);
    $sth->bindParam(':checked', $result['checked']);
    $sth->execute();

    
    print_r($sth->errorInfo());
}


?>