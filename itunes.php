<?php

require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT id FROM itunes_tracks WHERE wrapperType = '' ORDER BY RAND() LIMIT 1";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll();

foreach( $results as $result ){
	lookup_track_details( $result['id'] );
	usleep(1000000);
} 

//lookup_track_details(589877709);

function lookup_track_details( $track_id ){
	global $dbh;
	
	$url = "https://itunes.apple.com/lookup?id=" . $track_id;
	$json = file_get_contents( $url );
	$object = json_decode( $json );
	
	$track = $object->results[0];
	
	$artistName = utf8_decode($track->artistName);
	$trackName = utf8_decode($track->trackName);
	$previewUrl = $track->previewUrl;
	$wrapperType = $track->wrapperType;
	$kind = $track->kind;
	$artistId = $track->artistId;
	$collectionId = $track->collectionId;
	$collectionName = utf8_decode($track->collectionName);
	$collectionCensoredName = utf8_decode($track->collectionCensoredName);
	$trackCensoredName = utf8_decode($track->trackCensoredName);
	$artistViewUrl = $track->artistViewUrl;
	$collectionViewUrl = $track->collectionViewUrl;
	$trackViewUrl = $track->trackViewUrl;
	$artworkUrl30 = $track->artworkUrl30;
	$artworkUrl60 = $track->artworkUrl60;
	$artworkUrl100 = $track->artworkUrl100;
	$collectionPrice = $track->collectionPrice;
	$trackPrice = $track->trackPrice;
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
	
	if( $trackPrice === NULL ){
		$trackPrice = 0;
	}
	
	if( $collectionPrice === NULL ){
		$collectionPrice = 0;
	}
	
	$sql = "REPLACE INTO `itunes_tracks` (`id`, `artistName`, `trackName`, `previewUrl`, `wrapperType`, `kind`, `artistId`,`collectionId`,`collectionName`,`collectionCensoredName`, `trackCensoredName`, `artistViewUrl`,`collectionViewUrl`, `trackViewUrl`, `artworkUrl30`, `artworkUrl60`, `artworkUrl100`, `collectionPrice`, `trackPrice`, `releaseDate`, `collectionExplicitness`, `trackExplicitness`, `discCount`, `discNumber`, `trackCount`, `trackNumber`, `trackTimeMillis`, `country`, `currency`, `primaryGenreName`, `isStreamable`) VALUES ($track_id, :artistName, :trackName, '$previewUrl', '$wrapperType', '$kind', $artistId, $collectionId, :collectionName, :collectionCensoredName, :trackCensoredName, '$artistViewUrl', '$collectionViewUrl', '$trackViewUrl', '$artworkUrl30', '$artworkUrl60', '$artworkUrl100', $collectionPrice, $trackPrice, '$releaseDate', '$collectionExplicitness', '$trackExplicitness', $discCount, $discNumber, $trackCount, $trackNumber, $trackTimeMillis, '$country', '$currency', '$primaryGenreName', $isStreamable)"; 

	
	$sth = $dbh->prepare($sql);
		
	echo 'executing: ' . $sql . '</br>';
	$sth->execute(array(
	":trackName" => $trackName,
	':artistName' => $artistName,
	':trackCensoredName' => $trackCensoredName, 
	':collectionName' => $collectionName,
	':collectionCensoredName' => $collectionCensoredName
	));
}




?>