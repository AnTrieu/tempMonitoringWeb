<?php 
session_start();

$username = getenv('USER_DEFAULT');
$password = getenv('PASS_DEFAULT');

if(!empty($_POST['Name']))
{
	// Step 1
	$remote_url = 'http://127.0.0.1:15672/api/users/'.$_POST['Name'];
	$check_use = array(
		'http'=>array(
			'method'=>"GET",
			'header' => "Authorization: Basic " . base64_encode("$username:$password")
		)
	);
	
	// Open the file using the HTTP headers set above
	$file = file_get_contents($remote_url, false, stream_context_create($check_use));
	
	$file_decode = json_decode($file);	
	
	$update_use = array(
	  'http'=>array(
		'method'=>"PUT",
		'header' => "Authorization: Basic " . base64_encode("$username:$password"),
		'content' => json_encode(
						array(
							'password' => '123456789',
							'tags' => $file_decode->tags
						)
					)
	  )
	);	

	// Open the file using the HTTP headers set above
	$file = file_get_contents($remote_url, false, stream_context_create($update_use));	
			
	echo $file;
}
else
{
	echo "error";
}
?>