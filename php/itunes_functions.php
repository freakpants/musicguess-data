<?php
function lookup_collection( $collection_id , $country = "US"){
	echo 'looking up collection';
	$url = "https://itunes.apple.com/lookup?id=" . $collection_id . "&entity=song&country=" .$country;
	
	$json = file_get_contents( $url );
	$object = json_decode( $json );
	$counter = 0;
	
	echo '<pre>';
	var_dump($object->results);
	echo '</pre>'; 
	
	foreach($object->results as $song){
		// if( $counter > 0 ){
			
			lookup_track_details( $song->trackId , $country );
			usleep(1000000);
		// }
		// $counter++;
	}
}

function lookup_track_details( $track_id , $country = "US" ){
	global $dbh;
	
	switch($country){
		case 'NZL':
			$country = 'NZ';
		break;
		case 'AUS':
			$country = 'AU';
		break;
		case 'USA':
			$country = 'US';
		break;
		case 'GBR':
			$country = 'GB';
		break;
		case 'CAN':
			$country = 'CA';
		break;
		case 'CHE':
			$country = 'CH';
		break;
		case 'BLZ':
			$country = 'BZ';
		break;
		case 'MNG':
			$country = 'MN';
		break;
		case 'MLT':
			$country = 'MT';
		break;
		case 'FIN':
			$country = 'FI';
		break;
		case 'DEU':
			$country = 'DE';
		break;	
		case 'PHL':
			$country = 'PH';
		break;	
		case 'POL':
			$country = 'PL';
		break;		
		case 'AUT':
			$country = 'AT';
		break;		
		case 'HKG':
			$country = 'HK';
		break;	
	}
	
	
	$url = "https://itunes.apple.com/lookup?id=" . $track_id . "&country=" .$country;
	$json = file_get_contents( $url );
	$object = json_decode( $json );
	
	// echo 'url: '.$url.'</br>';
	// echo 'country: '.$country.'</br>';
	
	$track = $object->results[0];
	// echo '<pre>';
	// var_dump($track);
	// echo '</pre>';
	
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
	
	$sql = "REPLACE INTO `itunes_tracks` (`id`, `artistName`, `trackName`, `previewUrl`, `wrapperType`, `kind`, 
	`artistId`,`collectionId`,`collectionName`,`collectionCensoredName`, `trackCensoredName`, `artistViewUrl`,`collectionViewUrl`, `trackViewUrl`, `artworkUrl30`, 
	`artworkUrl60`, `artworkUrl100`, `collectionPrice`, `trackPrice`, `releaseDate`, `collectionExplicitness`, `trackExplicitness`, `discCount`, `discNumber`, `trackCount`, 
	`trackNumber`, `trackTimeMillis`, `country`, `currency`, `primaryGenreName`, `isStreamable`) 
	VALUES ($track_id, :artistName, :trackName, '$previewUrl', '$wrapperType', '$kind', $artistId, $collectionId, :collectionName, :collectionCensoredName, 
	:trackCensoredName, '$artistViewUrl', '$collectionViewUrl', '$trackViewUrl', '$artworkUrl30', '$artworkUrl60', '$artworkUrl100', $collectionPrice, $trackPrice, 
	'$releaseDate', '$collectionExplicitness', '$trackExplicitness', $discCount, $discNumber, $trackCount, $trackNumber, $trackTimeMillis, '$country', '$currency', 
	'$primaryGenreName', $isStreamable)"; 

	
	$sth = $dbh->prepare($sql);
		
	// echo 'executing: ' . $sql . '</br>';
	$sth->execute(array(
	":trackName" => $trackName,
	':artistName' => $artistName,
	':trackCensoredName' => $trackCensoredName, 
	':collectionName' => $collectionName,
	':collectionCensoredName' => $collectionCensoredName
	));
} 

?>