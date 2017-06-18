<?php
	
// output a playlist in json format

require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT id, name, description FROM playlists";

$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll();
	
 foreach( $results as $playlist ){
	create_playlist( $playlist['id']);
} 

// create_playlist( 13 );



function create_playlist( $playlist_id ){
	
	global $dbh;
	
	$sql = "SELECT track_id, service FROM songs_in_playlist WHERE playlist_id = $playlist_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$results = $sth->fetchAll();
	
	
	
	$tracks = [];
	foreach ($results as $track ){
		
		$track_id = $track['track_id'];
		
		switch( $track['service'] ){
			case 'itunes':
				$sql = "SELECT 'itunes' as service, id as id, artistName as artist, trackName as title, previewUrl as preview_url, collectionName as album, artworkUrl100 as album_art, trackViewUrl as buy_link FROM itunes_tracks WHERE id = $track_id";
			break;
		}
		
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$results = $sth->fetchAll();
		
		foreach ( $results[0] as $key => $value) {
			if( $key === 'artist' || $key === 'title' || $key === 'album' ){
				$results[$key] = utf8_encode($value);
			} elseif( gettype($key) === 'integer' ){
				unset($results[$key]); 
			} else {
				$results[$key] = $value;
			}
			// attempt to get album art from deezer
			if ( $key === 'album' ){
				$album_art = '';
				$title = '';
				$sql = "SELECT cover_xl, title FROM deezer_albums WHERE title LIKE '%$value%' LIMIT 1";
				$sth = $dbh->prepare($sql);
				$sth->execute();
				$inner_results = $sth->fetchAll();
				foreach( $inner_results as $result ){
					$album_art = $result['cover_xl'];
					$title = $result['title'];
				}
			}
		}
		if( $album_art != '' ){
			$results['album_art'] = $album_art;
			echo '<img style="width:200px" src="'.$results['album_art'].'" /></br>';
			echo 'Replaced album art for '.$results['artist'].' - '.$results['title'].' on album '.$results['album'].' <b>with</b> '.$title.'</br>';
		} else {
			echo 'Kept old album art for '.$results['artist'].' - '.$results['title'].' on album '.$results['album'].'</br>';
		}
		echo '</br></br>';
		
		
		array_push( $tracks, $results);
	}
	
	echo json_encode($tracks);
	
	$file = '../musicguess/game/playlists/playlist_' . $playlist_id . '.json';
	// Write the contents back to the file
	file_put_contents($file, json_encode($tracks));

}

?>