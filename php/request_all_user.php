<?php 
session_start();

// Get info account
$username = getenv('USER_DEFAULT');
$password = getenv('PASS_DEFAULT');

$remote_url = 'http://127.0.0.1:15672/api/users/';

// Create a stream
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header' => "Authorization: Basic " . base64_encode("$username:$password")                 
  )
);

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$file = file_get_contents($remote_url, false, $context);

echo $file;

?>