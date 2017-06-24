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
	// create_playlist( $playlist['id']);
} 


create_playlist( 0 );



function create_playlist( $playlist_id ){
	
	$kept_image = 0;
	$replaced_image = 0;
	
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
				$sql = "SELECT 'itunes' as service, id as id, artistName as artist, trackName as title, previewUrl as preview_url, collectionName as album, artworkUrl100 as album_art, trackViewUrl as buy_link, collectionId FROM itunes_tracks WHERE id = $track_id";
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
			
			if ( $key === 'collectionId' ){
				echo $value.'</br>';
			}
			
			// attempt to get album art from deezer
			if ( $key === 'album' ){
				$album_art = '';
				$title = '';
				$artist = utf8_decode($results['artist']);
				$sql = "SELECT cover_xl, title FROM deezer_albums WHERE title LIKE CONCAT('%', :value, '%') AND artist_name LIKE CONCAT('%', :artist_name, '%') LIMIT 1";
				
				$sth = $dbh->prepare($sql);
				$sth->execute( array(':value' => $value, ':artist_name' => $artist ) );
				$inner_results = $sth->fetchAll();
				
				foreach( $inner_results as $result ){
					$album_art = $result['cover_xl'];
					$title = $result['title'];
				}
				
				// try again with removed extra shit
				$title_replaced = ereg_replace(" \(.*\ Version\)","",$value);
				$title_replaced = ereg_replace(" - Single","",$title_replaced);
				$title_replaced = ereg_replace(" \(Version 1\)","",$title_replaced);
				$title_replaced = ereg_replace(" \(Deluxe\)","",$title_replaced);
				$title_replaced = ereg_replace(" - EP","",$title_replaced);
				$title_replaced = ereg_replace(" - The Hits","",$title_replaced);
				$title_replaced = ereg_replace("\(Remixes\) \[feat. Emma Lanford\]","",$title_replaced);
				$title_replaced = ereg_replace(" \(Remastered\)","",$title_replaced);
				$title_replaced = ereg_replace(" \(Bonus Track Version\)","",$title_replaced);
				$title_replaced = ereg_replace(" \+","",$title_replaced);
				$title_replaced = ereg_replace(" \(Remixes\)","",$title_replaced);
				$title_replaced = ereg_replace(" \[Remastered\]","",$title_replaced);
				$title_replaced = ereg_replace(" \(Flashdance\)","",$title_replaced);
				$title_replaced = ereg_replace(" \[Remixes\]","",$title_replaced);
				$title_replaced = ereg_replace(" \[Radio Edit\]","",$title_replaced);
				$title_replaced = ereg_replace(" \(feat\.\ .*\)","",$title_replaced);
				$title_replaced = ereg_replace(" \[feat\.\ .*\]","",$title_replaced);
				$title_replaced = ereg_replace(" \(.* [Ee]dition\)","",$title_replaced);
				$title_replaced = ereg_replace(" - .*\ Edition","",$title_replaced);
				$title_replaced = ereg_replace(" \(.* Edits)","",$title_replaced);
				$title_replaced = ereg_replace(" \[.*\]","",$title_replaced);
				
				
				echo $title_replaced.'</br>';
				echo 'song id: '.$results['id'].'</br>';
				
				$sth = $dbh->prepare($sql);
				$sth->execute( array(':value' => $title_replaced, ':artist_name' => $artist ) );
				
				$inner_results = $sth->fetchAll();
				foreach( $inner_results as $result ){
					$album_art = $result['cover_xl'];
					$title = $result['title'];
				}
			}
			if ( $key === "collectionId"){
				$sql = "SELECT image FROM itunes_album_image_replacements WHERE itunes_collection_id = $value";
				$sth = $dbh->prepare($sql);
				$sth->execute();
				$inner_results = $sth->fetchAll();
				foreach( $inner_results as $result ){
					$album_art = $result['image'];
				}
			}
		}
		
		if( $album_art != '' ){
			$replaced_image++;
			$results['album_art'] = $album_art;
			echo '<img style="width:200px" src="'.$results['album_art'].'" /></br>';
			echo 'Replaced album art for '.$results['artist'].' - '.$results['title'].' on album '.$results['album'].' <b>with</b> '.$title.'</br>';
		} else {
			$kept_image++;
			echo '<img style="width:200px" src="'.$results['album_art'].'" /></br>';
			echo 'Kept old album art for '.$results['artist'].' - '.$results['title'].' on album '.$results['album'].'</br>';
		}
		echo '</br></br>';
		
		
		array_push( $tracks, $results);
	}
	
	// echo json_encode($tracks);
	
	$file = '../musicguess/game/playlists/playlist_' . $playlist_id . '.json';
	// Write the contents back to the file
	file_put_contents($file, json_encode($tracks));
	
	echo '</br>'.$replaced_image.' replaced, '.$kept_image.' kept</br>';

}



?>