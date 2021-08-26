<head>
<script type="text/javascript" src="https://www.airconsole.com/api/airconsole-1.6.0.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Oswald|Roboto" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>
<body style="font-family:Roboto">
<?php

// output a collection to allow adding into playlist

$collection = 448049357;
$playlist = 12;
$pw = $_GET['pw'];

require("connect.php");

global $dbh;
$dbh = new PDO('mysql:host=localhost;dbname=' . $dbname , $user, $password);

$sql = "SET NAMES 'utf-8'";
$dbh->query($sql);

$sql = "SELECT trackName, artistName, id, previewUrl FROM itunes_tracks WHERE collectionId = " .$collection;
$sth = $dbh->prepare($sql);
$sth->execute();
$results = $sth->fetchAll();

echo '<table>';

foreach($results as $track){
	
	$id = $track['id'];
	
	$sql = "SELECT * FROM songs_in_playlist WHERE track_id = $id AND playlist_id = " . $playlist;
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$results = $sth->fetchAll();

	$class = "";
	if ( count($results) === 0 ){
		$class = "add";
	}
	
	echo '
	<tr>
		<td><div class="' . $class . '" track_id="' . $track['id'] . '" style="font-weight:bold;color:blue;cursor:pointer">' . $class . '</div></td>
		<td><div style="font-weight:bold;color:red;cursor:pointer" class="remove">Remove</div></td>
		<td>' . $track['artistName'] . '</td>
		<td>' . $track['trackName'] . '</td>
		<td><audio id="player" controls><source src="' . $track['previewUrl'] . '" type="audio/mpeg"></audio></td>
	</tr>';
}

echo '</table>';
?>
<script type="text/javascript">
$( document ).ready(function() {
	
	$( ".remove" ).click(function(element) {
		$(element.target).parent().parent().hide();
	});
	
	$( ".add" ).click(function(element) {

		track_id = $(element.target).attr('track_id');
		$(element.target).hide();

		$.ajax({ method: "POST", url: "ajax_add_to_playlist.php", data: { 
			pw: '<?= $pw ?>',
			track_id:track_id,
			playlist_id: <?= $playlist ?>,
			service: 'itunes',
		} , dataType: "json" })
			.done(function( msg ) {
			});
	});
});
</script>
</body>
