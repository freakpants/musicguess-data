<?php
// attempt to get a track id

require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

/* $sql = "SELECT DISTINCT artist FROM tracks ORDER BY RAND() LIMIT 1";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->§All(); */

/* foreach( $results as $result ){
	echo $result['artist'] . ' - ' . $result['title'] . '</br>';
	lookup_track( $result['artist'], $result['title'] );
	//usleep(100000);
} */

/* $sql = "SELECT DISTINCT id FROM deezer_artists WHERE 1 ORDER BY RAND()";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll();

foreach( $results as $result ){
	echo $result['id'] . '</br>';
	lookup_artist( $result['id'] );
	// usleep(100000);
} */

/* $sql = "SELECT id FROM deezer_albums WHERE artist_name = 'Amber'";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll();

foreach( $results as $result ){
	echo $result['id'] . '</br>';
	lookup_album( $result['id'] );
	// usleep(100000);
} */


lookup_album(114805);

function save_album_details ( $album_id, $album ){
	global $dbh;
	
	echo '<pre>';
	var_dump( $album );
	echo '</pre>';
	
	$title = utf8_decode($album->title);
	$cover = $album->cover;
	$cover_small = $album->cover_small;
	$cover_medium = $album->cover_medium;
	$cover_big = $album->cover_big;
	$cover_xl = $album->cover_xl;
	if ( $tracklist === '' ){
		$tracklist = $album->tracklist;
	} 
	$type = $album->type;
	
	$result_artist = utf8_decode($album->artist->name);
	
	$sql = "INSERT INTO `deezer_albums` (`id`, `title`, `cover`, `cover_small`, `cover_medium`, `cover_big`, `cover_xl`, `tracklist`, `type`, `artist_name`) VALUES ($album_id, :album_title, '$cover', '$cover_small', '$cover_medium', '$cover_big', '$cover_xl', '$tracklist', '$type', '$result_artist') ON DUPLICATE KEY UPDATE artist_name = '$result_artist'";
	$sth = $dbh->prepare($sql);
	$sth->execute(array(":album_title" => $title));

	echo 'executing album 2 sql. '.$sql.'</br></br>';
}

function lookup_album( $album_id, $artist_name ){
	global $dbh;

	$url = "https://api.deezer.com/album/" . $album_id;
	
	echo "checking api url ".$url.' </br>';
	$json = file_get_contents($url);
	$object = json_decode($json);

	foreach ( $object->tracks->data as $track ){
		save_track_to_db( $track , $object->tracklist, $object->id );
	}
	
	save_album_details ( $album_id, $object );
	
	/* echo '<pre>';
	var_dump($object);
	echo '</pre>'; */
	
	$sth->execute(); 
	
}

function save_track_to_db( $track, $tracklist = '', $album_id = '' ){
	global $dbh;
	
	/*
	echo '<pre>';
	var_dump($track);
	echo '</pre>'; */
	
	$result_title = utf8_decode($track->title);
	$result_artist = utf8_decode($track->artist->name);
	$id = $track->id;
	$readable = $track->readable ? 1 : 0;
	$title_short = $track->title_short;
	$title_version = $track->title_version;
	$link = $track->link;
	$duration = $track->duration;
	$rank = $track->rank;
	$explicit_lyrics = $track->explicit_lyrics ? 1 : 0;
	$preview = $track->preview;
	$artist_id = $track->artist->id;
	if( isset($track->album->id) ){
		$album_id = $track->album->id;
	}
	
	$type = $track->type;
	
	$sql = "INSERT INTO `deezer_tracks` (`id`, `readable`, `title`, `title_short`, `title_version`, `link`, `duration`, `rank`, `explicit_lyrics`, `preview`, `artist_id`, `album_id`, `type`) VALUES ($id, $readable, '$result_title', '$title_short', '$title_version', '$link', $duration, $rank, $explicit_lyrics, '$preview', $artist_id, $album_id , '$type')";
	$sth = $dbh->prepare($sql);
	
	echo 'executing: ' . $sql . '</br></br>';
	$sth->execute();
	
	echo 'executing sql. '.$sql.'</br></br>';
	
	$sth = $dbh->prepare($sql);
	$sth->execute();
}

function lookup_artist( $artist_id ){
	global $dbh;

	$url = "https://api.deezer.com/artist/" . $artist_id . "/top?limit=2000";
	$json = file_get_contents($url);
	$object = json_decode($json);
	 
	foreach( $object->data as $track ){
		$result_artist = utf8_decode($track->artist->name);
		save_track_to_db( $track );
	}
	
	$url = "https://api.deezer.com/artist/" . $artist_id . "/albums?limit=2000";
	$json = file_get_contents($url);
	$object = json_decode($json);
	
	foreach( $object->data as $album ){
		/* echo '<pre>';
		var_dump($album);
		echo '</pre>'; */
		
		$album_id = $album->id;
		$title = utf8_decode($album->title);
		$cover = $album->cover;
		$cover_small = $album->cover_small;
		$cover_medium = $album->cover_medium;
		$cover_big = $album->cover_big;
		$cover_xl = $album->cover_xl;
		$tracklist = $album->tracklist;
		$type = $album->type;
		
		$sql = "INSERT INTO `deezer_albums` (`id`, `title`, `cover`, `cover_small`, `cover_medium`, `cover_big`, `cover_xl`, `tracklist`, `type`, `artist_name`) VALUES ($album_id, :title, '$cover', '$cover_small', '$cover_medium', '$cover_big', '$cover_xl', '$tracklist', '$type', :artist_name ) ON DUPLICATE KEY UPDATE artist_name = :artist_name";

		$sth = $dbh->prepare($sql);
		$sth->execute( array(':title' => $title, ':artist_name' => $result_artist) );
		
		echo 'executing 1 album sql.'.$sql.'</br></br>';
	} 
	
	$url = "https://api.deezer.com/artist/" . $artist_id;
	$json = file_get_contents($url);
	$object = json_decode($json);
	
	$artist = $object;
	
	$result_artist = utf8_decode( $artist->name );
	$link = $artist->link;
	$picture = $artist->picture;
	$picture_small = $artist->picture_small;
	$picture_medium = $artist->picture_medium;
	$picture_big = $artist->picture_big;
	$picture_xl = $artist->picture_xl;
	if ( $tracklist === '' ){
		$tracklist = $artist->tracklist;
	} 
	
	$type = $artist->type;
	
	$sql = "INSERT INTO `deezer_artists` (`id`, `name`, `link`, `picture`, `picture_small`, `picture_medium`, `picture_big`, `picture_xl`, `tracklist`, `type`) VALUES ($artist_id, '$result_artist', '$link', '$picture', '$picture_small', '$picture_medium', '$picture_big', '$picture_xl', '$tracklist', '$type')
	ON DUPLICATE KEY UPDATE `name` = '$result_artist', `link` = '$link', `picture` = '$picture', `picture_small` = '$picture_small', `picture_medium` = '$picture_medium', `picture_big` = '$picture_big', `picture_xl` = '$picture_xl', `tracklist` = '$tracklist', `type` = '$type'";
	
	echo 'executing sql. '.$sql.'</br></br>';
	$sth = $dbh->prepare($sql);
	$sth->execute();
}

function lookup_track( $search_artist, $search_title ){
	global $dbh;

	$artist = urlencode($search_artist);
	$title = urlencode($search_title);

	$url = "http://api.deezer.com/search?q=" . $artist . '+' . $title;


	$json = file_get_contents($url);
	$object = json_decode($json);

	foreach( $object->data as $track ){
		$result_title = utf8_decode($track->title);
		$result_artist = utf8_decode($track->artist->name);
		$id = $track->id;
		$readable = $track->readable ? 1 : 0;
		$title_short = $track->title_short;
		$title_version = $track->title_version;
		$link = $track->link;
		$duration = $track->duration;
		$rank = $track->rank;
		$explicit_lyrics = $track->explicit_lyrics ? 1 : 0;
		$preview = $track->preview;
		$artist_id = $track->artist->id;
		$album_id = $track->album->id;
		$type = $track->type;
		

		
		if( $track->artist->name == $search_artist ){
			$sql = "INSERT INTO `deezer_tracks` (`id`, `readable`, `title`, `title_short`, `title_version`, `link`, `duration`, `rank`, `explicit_lyrics`, `preview`, `artist_id`, `album_id`, `type`) VALUES ($id, $readable, '$result_title', '$title_short', '$title_version', '$link', $duration, $rank, $explicit_lyrics, '$preview', $artist_id, $album_id , '$type')";
			$sth = $dbh->prepare($sql);
			$sth->execute();
			
			$album = $track->album;
			
			$title = utf8_decode($album->title);
			$cover = $album->cover;
			$cover_small = $album->cover_small;
			$cover_medium = $album->cover_medium;
			$cover_big = $album->cover_big;
			$cover_xl = $album->cover_xl;
			$tracklist = $album->tracklist;
			$type = $album->type;
			
			$sql = "INSERT INTO `deezer_albums` (`id`, `title`, `cover`, `cover_small`, `cover_medium`, `cover_big`, `cover_xl`, `tracklist`, `type`) VALUES ($album_id, :album_title, '$cover', '$cover_small', '$cover_medium', '$cover_big', '$cover_xl', '$tracklist', '$type')";
			$sth = $dbh->prepare($sql);
			$sth->execute(array(":album_title" => $title));
			
			$artist = $track->artist;

			$link = $artist->link;
			$picture = $artist->picture;
			$picture_small = $artist->picture_small;
			$picture_medium = $artist->picture_medium;
			$picture_big = $artist->picture_big;
			$picture_xl = $artist->picture_xl;
			$tracklist = $artist->tracklist;
			$type = $artist->type;
			
			$sql = "INSERT INTO `deezer_artists` (`id`, `name`, `link`, `picture`, `picture_small`, `picture_medium`, `picture_big`, `picture_xl`, `tracklist`, `type`) VALUES ($artist_id, '$result_artist', '$link', '$picture', '$picture_small', '$picture_medium', '$picture_big', '$picture_xl', '$tracklist', '$type')";
			
			$sth = $dbh->prepare($sql);
			$sth->execute();
		}
		
	}
}

?>