<?php
// helper functions that are used by multiple filesize

function get_album_art($artist = '', $title = '', $collectionId = 0){
	global $dbh;
	
	
	$album_art = '';
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
	$title_replaced = preg_replace("/ \(.* Version\)/","",$original_title);
	$title_replaced = preg_replace("/ \[.* Version\]/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.* [eE]dition\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.* Editon\)/","",$title_replaced);
	$title_replaced = preg_replace("/ - .*\ Edition/","",$title_replaced);
	$title_replaced = preg_replace("/ - EP/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Live\)/","",$title_replaced);
	$title_replaced = preg_replace("/ - Single/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Single\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \[Remastered\]/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Remastered\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Remastered .*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Remaster.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Jubil.*umsedition\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Flashdance\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Version 1\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Deluxe\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Remixes\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \[Remixes\]/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Remixes.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(feat\.\ .*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \[feat\.\ .*\]/","",$title_replaced);
	$title_replaced = preg_replace("/ \[.* Edit\]/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.* Edits\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \+/","",$title_replaced);
	$title_replaced = preg_replace("/\.\.\./","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Soundtrack.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Re-Recorded\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(Bonus.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ 2008/","",$title_replaced);
	$title_replaced = preg_replace("/".$artist." - /","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Anthem.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Mixed.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Reissue.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Expanded.*\)/","",$title_replaced);
	
	/* 
	$title_replaced = preg_replace(" - The Hits","",$title_replaced);
	$title_replaced = preg_replace(" \[feat. Emma Lanford\]","",$title_replaced);
	$title_replaced = preg_replace(" \(Bonus Track Version\)","",$title_replaced);
	$title_replaced = preg_replace(" \[feat\.\ .*\]","",$title_replaced);
	$title_replaced = preg_replace(" \(.* [Ee]dition\)","",$title_replaced);
	$title_replaced = preg_replace(" \[.*\]","",$title_replaced);
	$title_replaced = preg_replace(" \(Original Mix\)","",$title_replaced);
	$title_replaced = preg_replace(" Live","",$title_replaced);
	$title_replaced = preg_replace(" \(.*Motion Picture.*\)","",$title_replaced);
	 */
	
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
	
	return array('album_art' => $album_art, 'title' => $title_replaced);
} 			
	

?>