<?php

function lookup_album_on_deezer( $search_album){
    global $dbh;

    $search_album = urlencode($search_album);

    $url = 'https://api.deezer.com/search?q=album:"'.$search_album.'"';

    $json = @file_get_contents($url);

    if($json != FALSE){
        $object = json_decode($json);
    }
	    

    /* echo '<pre>';
    var_dump($object->data);
    echo '</pre>'; */
    if(!isset($object->data)){
        return;
    }
	foreach( $object->data as $track ){
        // echo 'entering deezer track';
		$result_title = utf8_decode($track->title);
		$result_artist = utf8_decode($track->artist->name);
		$id = $track->id;
		$readable = $track->readable ? 1 : 0;
		$title_short = $track->title_short;
        if(isset($track->title_version)){
            $title_version = $track->title_version;
        } else {
            $title_version = "";
        }        
		
		$link = $track->link;
		$duration = $track->duration;
		$rank = $track->rank;
		$explicit_lyrics = $track->explicit_lyrics ? 1 : 0;
		$preview = $track->preview;
		$artist_id = $track->artist->id;
		$album_id = $track->album->id;
		$type = $track->type;
		

		
		
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
        $artist_name = $track->artist->name;
        
        $sql = "REPLACE INTO `deezer_albums` (`id`, `title`, `cover`, `cover_small`, `cover_medium`, `cover_big`, `cover_xl`, `tracklist`, `type`, `artist_name`) 
        VALUES ($album_id, :album_title, '$cover', '$cover_small', '$cover_medium', '$cover_big', '$cover_xl', '$tracklist', '$type', '$artist_name')";
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
    return $url;
}

function lookup_track( $search_artist, $search_title ){
	global $dbh;

	$artist = urlencode($search_artist);
	$title = urlencode($search_title);

    if($artist !== ''){
        $url = "http://api.deezer.com/search?q=" . $artist . '&' . $title;
    } else {
        $url = "http://api.deezer.com/search?q=" . $title;
    }   
	
    // echo $url;


	$json = file_get_contents($url);
	$object = json_decode($json);

    /* echo '<pre>';
    var_dump($object->data);
    echo '</pre>'; */
    if(!isset($object->data)){
        return;
    }
	foreach( $object->data as $track ){
        // echo 'entering deezer track';
		$result_title = utf8_decode($track->title);
		$result_artist = utf8_decode($track->artist->name);
		$id = $track->id;
		$readable = $track->readable ? 1 : 0;
		$title_short = $track->title_short;
        if(isset($track->title_version)){
            $title_version = $track->title_version;
        } else {
            $title_version = "";
        }        
		
		$link = $track->link;
		$duration = $track->duration;
		$rank = $track->rank;
		$explicit_lyrics = $track->explicit_lyrics ? 1 : 0;
		$preview = $track->preview;
		$artist_id = $track->artist->id;
		$album_id = $track->album->id;
		$type = $track->type;
		

		
		
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
        $artist_name = $track->artist->name;
        
        $sql = "REPLACE INTO `deezer_albums` (`id`, `title`, `cover`, `cover_small`, `cover_medium`, `cover_big`, `cover_xl`, `tracklist`, `type`, `artist_name`) 
        VALUES ($album_id, :album_title, '$cover', '$cover_small', '$cover_medium', '$cover_big', '$cover_xl', '$tracklist', '$type', '$artist_name')";
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
?>