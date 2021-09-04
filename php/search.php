<?php
require("connect.php");
require("itunes_functions.php");
require("deezer_functions.php");
require("functions.php");
global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$term = urlencode(utf8_encode($_GET['artist'])).'+'.urlencode(utf8_encode($_GET['title'])).'+'.urlencode(utf8_encode($_GET['album']));

// lookup the track in deezer
lookup_track( $_GET['artist'], $_GET['title'] );

if(isset($_GET['country'])){
    $url = 'https://itunes.apple.com/search?term='.$term.'&entity=song&country='.$_GET['country'];
} else {
    $url = 'https://itunes.apple.com/search?term='.$term.'&entity=song';
}


$json = file_get_contents( $url );

$sql = "SELECT id as trackId, artistName, trackName, collectionName, releaseDate FROM itunes_tracks 
WHERE artistName LIKE CONCAT('%', :artistName, '%') AND trackName LIKE CONCAT('%', :trackName, '%') AND collectionName LIKE CONCAT('%', :collectionName, '%') ORDER BY collectionName, releaseDate ASC";
$sth = $dbh->prepare($sql);
$sth->execute( array(':artistName' => $_GET['artist'], ':trackName' => $_GET['title'], ':collectionName' => $_GET['album']) );
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

// copy for manipulation
// $merged = $results;

/* echo '<pre>';
var_dump($results);
echo '</pre>'; */

$object = json_decode( $json );
$counter = 0;

foreach($object->results as $track){
	$artistName = $track->artistName;
	$trackName = $track->trackName;
	$previewUrl = $track->previewUrl;
	$wrapperType = $track->wrapperType;
	$kind = $track->kind;
	$artistId = $track->artistId;
	$collectionId = $track->collectionId;
	$collectionName = $track->collectionName;
	$collectionCensoredName = $track->collectionCensoredName;
	$trackCensoredName = $track->trackCensoredName;
	$artistViewUrl = $track->artistViewUrl;
	$collectionViewUrl = $track->collectionViewUrl;
	$trackViewUrl = $track->trackViewUrl;
	$artworkUrl30 = $track->artworkUrl30;
	$artworkUrl60 = $track->artworkUrl60;
	$artworkUrl100 = $track->artworkUrl100;
    if(isset($track->collectionPrice)){
        $collectionPrice = $track->collectionPrice;
    } else {
        $collectionPrice = 0;
    }
	if(isset($track->trackPrice)){
        $trackPrice = $track->trackPrice;
    } else {
        $trackPrice = 0;
    }
	$releaseDate = $track->releaseDate;
	$collectionExplicitness = $track->collectionExplicitness;
	$trackExplicitness = $track->trackExplicitness;
	$discCount = $track->discCount;
	$discNumber = $track->discNumber;
	$trackCount = $track->trackCount;
	$trackNumber = $track->trackNumber;
	$trackTimeMillis = $track->trackTimeMillis;
	$country = $track->country;
	$currency = $track->currency;
	$primaryGenreName = $track->primaryGenreName;
	$isStreamable = $track->isStreamable ? 1 : 0;
	
	if($artistId === 0 || $artistId === '' || $artistId === NULL){
		return;
	}
	
	$sql = "REPLACE INTO `itunes_tracks` (`id`, `artistName`, `trackName`, `previewUrl`, `wrapperType`, `kind`, 
	`artistId`,`collectionId`,`collectionName`,`collectionCensoredName`, `trackCensoredName`, `artistViewUrl`,`collectionViewUrl`, `trackViewUrl`, `artworkUrl30`, 
	`artworkUrl60`, `artworkUrl100`, `collectionPrice`, `trackPrice`, `releaseDate`, `collectionExplicitness`, `trackExplicitness`, `discCount`, `discNumber`, `trackCount`, 
	`trackNumber`, `trackTimeMillis`, `country`, `currency`, `primaryGenreName`, `isStreamable`) 
	VALUES ($track->trackId, :artistName, :trackName, '$previewUrl', '$wrapperType', '$kind', $artistId, $collectionId, :collectionName, :collectionCensoredName, 
	:trackCensoredName, '$artistViewUrl', '$collectionViewUrl', '$trackViewUrl', '$artworkUrl30', '$artworkUrl60', '$artworkUrl100', $collectionPrice, $trackPrice, 
	'$releaseDate', '$collectionExplicitness', '$trackExplicitness', $discCount, $discNumber, $trackCount, $trackNumber, $trackTimeMillis, '$country', '$currency', 
	'$primaryGenreName', $isStreamable)"; 

    	
    // attempt to get album art from deezer
	$album_data = get_album_art($artistName,$collectionName,$collectionId);
	$album_art = $album_data['album_art'];

    // save album art
    if(isset($album_art) && $album_art !== ''){
        copy($album_art, '../musicguess/game/album_art/' . $collectionId . ".jpg" );
    }
	
	$sth = $dbh->prepare($sql);
		
	// echo 'executing: ' . $sql . '</br>';
	$sth->execute(array(
	":trackName" => $trackName,
	':artistName' => $artistName,
	':trackCensoredName' => $trackCensoredName, 
	':collectionName' => $collectionName,
	':collectionCensoredName' => $collectionCensoredName
	));
    /* foreach ($results as $result){
        if($result['trackId'] !== $track->trackId){
            $merged = array_merge($merged, $track);
        }
    } */
}

echo json_encode(array_merge($results, $object->results));
	
