<?php
// attempt oauth login to get a preview track

$oauth_consumer_key = '7d64mq9mvjj3';
$oauth_consumer_secret = 'qjwcf4r9y6ujzfgj';

// first look up a track by artist & title

$title = "";
$artist = 'Eminem';

$url = "http://api.7digital.com/1.2/track/search?q=" . $title . "+" . $artist ."&oauth_consumer_key=" . $oauth_consumer_key . "&country=WW&pagesize=50"; 


/* 
// create a new cURL resource
$ch = curl_init();
// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// make sure the API returns json, not xml
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

// grab URL and pass it to the browser
$json = curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);

$object = json_decode($json); 

$trackid = $object->searchResults->searchResult[0]->track->id;

*/
$trackid = 55576146;

$releaseid = 535192;
$artistid = 214;
// get details for release
$url = "http://api.7digital.com/1.2/release/details?releaseid=" . $releaseid . " &oauth_consumer_key=" . $oauth_consumer_key . "&country=WW";

echo '<pre>';
var_dump($url);
echo '</pre>';


$oauth_nonce = bin2hex(random_bytes(5));
$oauth_timestamp = time();

// attempt to oauth authenticate

$url = "http://previews.7digital.com/clip/" . $trackid . "?country=WW&oauth_consumer_key=" . $oauth_consumer_key . "&oauth_nonce=" . $oauth_nonce . "&oauth_signature_method=HMAC-SHA1&oauth_timestamp=" . $oauth_timestamp . "&oauth_version=1.0";

$base_string = 
"GET&" . 
urlencode("http://previews.7digital.com/clip/" . $trackid) . "&" . urlencode("country=WW&oauth_consumer_key=" . $oauth_consumer_key . "&oauth_nonce=" . $oauth_nonce . "&oauth_signature_method=HMAC-SHA1&oauth_timestamp=" . $oauth_timestamp . "&oauth_version=1.0");

$key = urlencode($oauth_consumer_secret) . "&";

$signature = hash_hmac( 'sha1', $base_string, $key , true);
$signature = base64_encode($signature);
$signature = urlencode($signature);

$url .= '&oauth_signature=' . $signature ;

echo '<pre>';
var_dump($url);
echo '</pre>';

?>
<audio id="player" controls autoplay> <source src="<?= $url ?>" type="audio/mpeg"></audio>
