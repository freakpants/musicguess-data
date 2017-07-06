<?php
// helper functions that are used by multiple filesize

function get_album_art($artist = '', $title = '', $collectionId = 0){
	global $dbh;
	
	$artist = utf8_decode($artist);
	$original_title = utf8_decode($title);
	
	$sql = "SELECT cover_xl, title FROM deezer_albums WHERE title LIKE CONCAT('%', :title, '%') AND artist_name LIKE CONCAT('%', :artist_name, '%') LIMIT 1";
	
	$sth = $dbh->prepare($sql);
	$sth->execute( array(':title' => $original_title, ':artist_name' => $artist ) );
	$inner_results = $sth->fetchAll();
	
	foreach( $inner_results as $result ){
		$album_art = $result['cover_xl'];
		$title = $result['title'];
	}
	
	// try again with removed extra shit
	$title_replaced = ereg_replace(" \(.*\ Version\)","",$original_title);
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
	$title_replaced = ereg_replace(" \(Original Mix\)","",$title_replaced);
	$title_replaced = ereg_replace("\.\.\.","",$title_replaced);
	$title_replaced = ereg_replace(" Live","",$title_replaced);
	$title_replaced = ereg_replace(" \(Bonus Tracks\)","",$title_replaced);
	$title_replaced = ereg_replace(" \(Remasteris.*\)","",$title_replaced);
	$title_replaced = ereg_replace(" \(Original Motion Picture Soundtrack\)","",$title_replaced);
	 
	
	 
	
	echo 'replaced title:'.$title_replaced.'</br>';
	
	$sth = $dbh->prepare($sql);
	$sth->execute( array(':title' => $title_replaced, ':artist_name' => $artist ) );
	
	$inner_results = $sth->fetchAll();
	foreach( $inner_results as $result ){
		$album_art = $result['cover_xl'];
		$title = $result['title'];
	}

	$sql = "SELECT image FROM itunes_album_image_replacements WHERE itunes_collection_id = $collectionId";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$inner_results = $sth->fetchAll();
	foreach( $inner_results as $result ){
		$album_art = $result['image'];
	}
	
	echo 'album art:'.$album_art.'</br>';
	
	return $album_art;
} 			
	

?>