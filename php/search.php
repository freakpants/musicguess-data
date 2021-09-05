<?php
require("connect.php");
require("itunes_functions.php");
require("deezer_functions.php");
require("functions.php");
global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$term = urlencode(utf8_encode($_GET['artist'])).'+'.urlencode(utf8_encode($_GET['title'])).'+'.urlencode(utf8_encode($_GET['album']));

// lookup the track in deezer
// echo 'searching';
lookup_track( $_GET['artist'], $_GET['title'] );

if(isset($_GET['country'])){
    $url = 'https://itunes.apple.com/search?term='.$term.'&entity=song&country='.$_GET['country'];
} else {
    $url = 'https://itunes.apple.com/search?term='.$term.'&entity=song';
}


$json = file_get_contents( $url );

$sql = "SELECT id as trackId, previewUrl, artistName, trackName, collectionName, releaseDate, collectionId FROM itunes_tracks 
WHERE artistName LIKE CONCAT('%', :artistName, '%') AND trackName LIKE CONCAT('%', :trackName, '%') AND collectionName LIKE CONCAT('%', :collectionName, '%') ORDER BY collectionName, releaseDate ASC";
$sth = $dbh->prepare($sql);
if(isset($_GET['artist']) && $_GET['artist'] !== ''){
    $artist = $_GET['artist'];
} else {
    $artist = '';
}
if(isset($_GET['title']) && $_GET['title'] !== ''){
    $title = $_GET['title'];
} else {
    $title = '';
}
if(isset($_GET['album']) && $_GET['album'] !== ''){
    $album = $_GET['album'];
} else {
    $album = '';
}

$sth->execute( array(':artistName' => $artist, ':trackName' => $title, ':collectionName' => $album ));
$results = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($results as $result){
    $return_array[$result['trackId']] = $result;
}


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
    if(isset($track->previewUrl)){
        $previewUrl = $track->previewUrl;
    } else {
        $previewUrl = "";
    }

	$wrapperType = $track->wrapperType;
	$kind = $track->kind;
	$artistId = $track->artistId;
	$collectionId = $track->collectionId;
	$collectionName = $track->collectionName;
	$collectionCensoredName = $track->collectionCensoredName;
	$trackCensoredName = $track->trackCensoredName;
    if(isset($track->artistViewUrl)){
	    $artistViewUrl = $track->artistViewUrl;
    }
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
    if(isset($track->trackTimeMillis)){
	    $trackTimeMillis = $track->trackTimeMillis;
    } else{
        $trackTimeMillis = "";
    }
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

    if(! isset($return_array[$track->trackId])){
        $return_array[$track->trackId] = $track;
    }
    /* foreach ($results as $result){
        if($result['trackId'] !== $track->trackId){
            $merged = array_merge($merged, $track);
        }
    } */
}

$json_array = array();
if(isset($return_array)){
    foreach($return_array as $key => $value){
        $sql = "SELECT relation_id, playlists.id as id FROM songs_in_playlist LEFT JOIN playlists ON songs_in_playlist.playlist_id = playlists.id  WHERE `track_id` = :id";
        $sth = $dbh->prepare($sql);
        $sth->execute(array(":id" => $key));
        $results = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(is_array($value)){
            $value['relation_ids'] = array();
            $value['playlist_ids'] = array();
            foreach($results as $result){
                array_push($value['relation_ids'], $result['relation_id']);
                array_push($value['playlist_ids'], $result['id']);
            }
        } else {
            $value->relation_ids = array();
            $value->playlist_ids = array();
            foreach($results as $result){
                array_push($value->relation_ids, $result['relation_id']);
                array_push($value->playlist_ids, $result['id']);
            }
        }
    
        // $value['playlist_ids'] = $results['playlist_'];
    
        array_push( $json_array, $value);
    }
}


// echo json_encode($results);
echo json_encode($json_array);
	
