<?php
require("connect.php");
require("itunes_functions.php");
require("deezer_functions.php");
require("functions.php");
global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$meta_array = array();
$meta_array['album_datas'] = array();

/* echo '<pre>';
var_dump($_GET);
echo '</pre>'; */

// $term = urlencode(utf8_encode($_GET['artist'])).'+'.urlencode(utf8_encode($_GET['title'])).'+'.urlencode(utf8_encode($_GET['album']));
$term = urlencode($_GET['artist']).'+'.urlencode($_GET['title']).'+'.urlencode($_GET['album']);

// lookup the track in deezer
// echo 'searching';
lookup_track( $_GET['artist'], $_GET['title'] );

// lookup the album in deezer
$meta_array['album_search_url'] = lookup_album_on_deezer($_GET['album']);


if(isset($_GET['country'])){
    $url = 'https://itunes.apple.com/search?term='.$term.'&entity=song&country='.$_GET['country'];
} else {
    $url = 'https://itunes.apple.com/search?term='.$term.'&entity=song';
}

// echo $url.'</br>';
$json = file_get_contents( $url );

/* echo '<pre>';
var_dump($json);
echo '</pre>'; */

$sql = "SELECT id as trackId, previewUrl, artistName, trackName, collectionName, releaseDate, collectionId, checked FROM itunes_tracks 
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

// get the output before debugDumpParams() get executed 
$before = ob_get_contents();
//start a new buffer
ob_start();
// dump params now
$sth->debugDumpParams();
// save the output in a new variable $data
$meta_array['sql_track_lookup'] = ob_get_contents();
// clean the output screen
ob_end_clean();

$results = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($results as $result){
    $return_array[$result['trackId']] = $result;
    
    // attempt to get album art from deezer
	$album_data = get_album_art($result['artistName'],$result['collectionName'],$result['collectionId']);
	$album_art = $album_data['album_art'];
    
    array_push($meta_array['album_datas'], $album_data);

    // save album art
    if(isset($album_art) && $album_art !== ''){
        copy($album_art, '../musicguess/game/album_art/' . $result['collectionId'] . ".jpg" );
    }
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
    
    if(!(isset($track->previewUrl)) || strpos($track->previewUrl, "http\:") !== false ){
        // don't add tracks that have no preview url, because we cant play those
        // also don't add tracks that are not on https
        continue;
    } else {
        $previewUrl = $track->previewUrl;
    }

	$wrapperType = $track->wrapperType;
	$kind = $track->kind;
	$artistId = $track->artistId;
	$collectionId = $track->collectionId;
	$collectionName = $track->collectionName;

    lookup_album_on_deezer($collectionName);

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
    if(isset($track->releaseDate)){
	    $releaseDate = $track->releaseDate;
    } else {
        $releaseDate = '';
    }
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
    if(isset($track->primaryGenreName)){
        $primaryGenreName = $track->primaryGenreName;
    } else {
        $primaryGenreName = "";
    }
	
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
    
    array_push($meta_array['album_datas'], $album_data);

    // save album art
    if(isset($album_art) && $album_art !== ''){
        copy($album_art, '../musicguess/game/album_art/' . $collectionId . ".jpg" );
    }
	
	$sth = $dbh->prepare($sql);
		
	// echo 'executing: ' . $sql . '</br>';

    // skip adding the track to the array if it was already returned by the search, also dont update the database
    if(! isset($return_array[$track->trackId])){
        $return_array[$track->trackId] = $track;
        $sth->execute(array(
            ":trackName" => $trackName,
            ':artistName' => $artistName,
            ':trackCensoredName' => $trackCensoredName, 
            ':collectionName' => $collectionName,
            ':collectionCensoredName' => $collectionCensoredName
            ));
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
        // the results from itunes are objects, the results from the database are associative arrays, therefore we need to add them to our list differently
        if(is_array($value)){
            $value['relation_ids'] = array();
            $value['playlist_ids'] = array();
            foreach($results as $result){
                array_push($value['relation_ids'], $result['relation_id']);
                array_push($value['playlist_ids'], $result['id']);
            }
        } else {
            $value->checked = "0";
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
$meta_array['tracks'] = $json_array;
// echo json_encode($results);
echo json_encode($meta_array);
	
