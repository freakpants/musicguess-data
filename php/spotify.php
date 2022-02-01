<?php 
$_POST = json_decode(file_get_contents("php://input"),true);

$items = $_POST['data'];
foreach($items as $item){
    $track = $item['track'];
    echo $track['artists'][0]['name'] . " - ";
    echo $track['name'];
    echo "\r\n";
    
} 

/* 
print_r($_POST['data'][0]['track']['name']);
print_r($_POST['data'][0]['track']['artists']); */
?>