<?php
// helper functions that are used by multiple files

function get_album_art($artist = '', $title = '', $collectionId = 0){
	global $dbh;
	
	
	$album_art = '';
	$artist = $artist;
	$original_title = $title;
	
	
	
	// $sql = "SELECT cover_xl, title FROM deezer_albums WHERE title LIKE CONCAT('%', :title, '%') AND artist_name LIKE CONCAT('%', :artist_name, '%') LIMIT 1";
	
	$query_title = utf8_decode($original_title);
	$query_artist =  utf8_decode($artist);
	
	$sql = "SELECT cover_xl, title FROM deezer_albums WHERE title LIKE %$query_title% AND artist_name LIKE %$query_artist% LIMIT 1";
	
	$select_deezer_album_statement = $dbh->prepare($sql);
	$select_deezer_album_statement->execute();
	$inner_results = $select_deezer_album_statement->fetchAll();
	
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
	$title_replaced = preg_replace("/ \[Remastered .*\]/","",$title_replaced);
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
	if($artist !== ''){
		// $title_replaced = preg_replace("/".$artist." - /","",$title_replaced);
	}
	
	$title_replaced = preg_replace("/ \(.*Anthem.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Mixed.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Reissue.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Expanded.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*digitally remastered.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*Opera Version.*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*(Motion Picture Version).*\)/","",$title_replaced);
	$title_replaced = preg_replace("/ \(.*(From \?Chess\?).*\)/","",$title_replaced);

	// new regexes
	$title_replaced = preg_replace("/\[.*Remix\]/","",$title_replaced);
	$title_replaced = preg_replace("/\(.*Remix\)/","",$title_replaced);
	$title_replaced = preg_replace("/\(Instrumental\)/","",$title_replaced);

	
	// order is important here
	$title_replaced = preg_replace("/(&)/","And",$title_replaced);
	$title_replaced = preg_replace("/Reich And/","&",$title_replaced);

	
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
	
	// echo "Looking for: ".$artist." - ".$title_replaced."\r\n";
	
	$sth = $dbh->prepare($sql);
	$sth->execute( );
	
	// var_dump("SELECT cover_xl, title FROM deezer_albums WHERE title LIKE '%".$title_replaced."%' AND artist_name LIKE '%".$artist."%' LIMIT 1");
	
	$inner_results = $sth->fetchAll();
	foreach( $inner_results as $result ){
		$album_art = $result['cover_xl'];
		$title = $result['title'];
	}

	$replacement_sql = "SELECT image FROM itunes_album_image_replacements WHERE itunes_collection_id = $collectionId";
	$sth = $dbh->prepare($replacement_sql);
	$sth->execute();
	$inner_results = $sth->fetchAll();
	foreach( $inner_results as $result ){
		$album_art = $result['image'];
	}
	
	// echo 'album art:'.$album_art.'</br>';
	
	// get the output before debugDumpParams() get executed 
	$before = ob_get_contents();
	//start a new buffer
	ob_start();
	// dump params now
	$select_deezer_album_statement->debugDumpParams();
	// save the output in a new variable $data
	$data = ob_get_contents();
	// clean the output screen
	ob_end_clean();


	return array('album_art' => $album_art, 'title' => $title_replaced, 'sql' => utf8_encode($sql), 'select_deezer_album_statement' => utf8_encode($data));
} 			
	

?>