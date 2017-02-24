<?php
// compile a list of available playlists with additional info in json format
	
require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

create_playlist_info();

function create_playlist_info(){
	
	global $dbh;
	
	$sql = "SELECT id, name, description FROM playlists WHERE public = 1";
	// $sql = "SELECT id, name, description FROM playlists";

	$sth = $dbh->prepare($sql);
	$sth->execute();
	$results = $sth->fetchAll();
	$array_of_playlists = [];
	
	foreach( $results as $playlist ){
		
		$playlist_object =  new stdClass();
		$playlist_object->name = $playlist['name'];
		$playlist_object->id = $playlist['id'];
		$playlist_object->description = $playlist['description'];
		
		// determine amount of tracks
		$id = $playlist['id'];
		$sql = "SELECT count(*) FROM songs_in_playlist WHERE playlist_id = $id";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$results = $sth->fetch();
		$playlist_object->amount_of_tracks = (int) $results['count(*)'];
		
		// get 4 random images that will represent the playlist
		$sql = "SELECT track_id, service FROM songs_in_playlist WHERE playlist_id = $id ORDER BY RAND() LIMIT 4";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$results = $sth->fetchAll();
		
		$playlist_object->album_art = [];
		foreach( $results as $track ){
			$id = $track['track_id'];
			
			switch( $track['service'] ){
				case 'itunes':
					$sql = "SELECT artworkUrl100 as album_art FROM itunes_tracks WHERE id = $id";
				break;
			}

			$sth = $dbh->prepare($sql);
			$sth->execute();
			$results = $sth->fetch();
			
			array_push( $playlist_object->album_art, $results['album_art']);
			
		}
		
		array_push( $array_of_playlists, $playlist_object );
		
	}
	
	echo json_encode($array_of_playlists);

}

	
?>