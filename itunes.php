<head>
<script type="text/javascript" src="https://www.airconsole.com/api/airconsole-1.6.0.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Oswald|Roboto" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>
<body style="font-family:Roboto">
<?php
require("connect.php");

require("itunes_functions.php");
global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT id FROM itunes_tracks WHERE wrapperType = '' ORDER BY RAND() LIMIT 1";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll();

/* foreach( $results as $result ){
	lookup_track_details( $result['id'] );
	usleep(1000000);
} */

lookup_collection(424149647, 'gb');
// lookup_track_details(589877709);


/* attempt matching from list from db */

// playlist to add the songs to
$playlist = 0	;

// pw for allowing ajax to execute
$pw = $_GET['pw'];

$sql = "SELECT id, artist, title FROM lookup WHERE added = 0 ORDER BY RAND() LIMIT 6";
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll();

echo '<style>
table td, table th {
    border: 1px solid black;
}
</style>
';

foreach( $results as $result ){
	
	$term = urlencode(utf8_encode($result['artist'])).'+'.urlencode(utf8_encode($result['title']));
	$url = 'https://itunes.apple.com/search?term='.$term.'&entity=song';
	echo '<a href="'.$url.'">'.$result['artist'].' - '.$result['title'].'</a></br>';
	
	$json = file_get_contents( $url );
	$object = json_decode( $json );
	
	$counter = 0;
	echo '<table style="border:1px solid black; border-collapse:collapse;"><tr><th>Artist</th><th>Song</th><th>Album</th><th>Released</th><th>Country</th><th></th><th></th></tr>';
	foreach($object->results as $song){
		if( $counter > 0 ){
			// lookup_track_details( $song->trackId , $country );
			usleep(1000000);
		}
		$counter++;
		
		
		
		echo '<tr>
			<th>'.$song->artistName.'</th>
			<th>'.$song->trackName.'</th>
			<th>'.$song->collectionName.'</th>
			<th>'.$song->releaseDate.'</th>
			<th>'.$song->country.'</th>
			<th><div class="add_collection" collection_id="' . $song->collectionId . '" style="font-weight:bold;color:blue;cursor:pointer">Add Collection</div></th>
			<th><div class="add_track" lookup_id="'.$result['id'].'" track_id="' . $song->trackId . '" style="font-weight:bold;color:blue;cursor:pointer">Add Track to Playlist</div></th>
			</tr>';
	}
	echo '</table>';
} 
?>
<script type="text/javascript">
$( document ).ready(function() {
	
	$( ".remove" ).click(function(element) {
		$(element.target).parent().parent().hide();
	});
	
	$( ".add_collection" ).click(function(element) {

		collection_id = $(element.target).attr('collection_id');
		$(element.target).hide();

		$.ajax({ method: "POST", url: "ajax_lookup_collection.php", data: { 
			pw: '<?= $pw ?>',
			collection_id:collection_id,
		} , dataType: "json" })
			.done(function() {
				alert("collection added");
			});
	});
	
	$( ".add_track" ).click(function(element) {

		track_id = $(element.target).attr('track_id');
		lookup_id = $(element.target).attr('lookup_id');
		$(element.target).hide();

		$.ajax({ method: "POST", url: "ajax_add_to_playlist.php", data: { 
			pw: '<?= $pw ?>',
			track_id:track_id,
			playlist_id: <?= $playlist ?>,
			service: 'itunes',
			lookup_id: lookup_id,
		} , dataType: "json" })
			.done(function( msg ) {
			});
	});
});
</script>
