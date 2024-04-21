<?php
	session_start();
			
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Set session variables
		$_SESSION["username"] = $_POST['username'];
		$_SESSION["password"] = $_POST['password'];

		// Get info account
		$username = getenv('USER_DEFAULT');
		$password = getenv('PASS_DEFAULT');
	
		// Step 1
		$remote_url = 'http://127.0.0.1:15672/api/users/'.$_SESSION["username"];
		$check_use = array(
		  'http'=>array(
			'method'=>"GET",
			'header' => "Authorization: Basic " . base64_encode("$username:$password")
		  )
		);
	
		// Open the file using the HTTP headers set above
		$file = file_get_contents($remote_url, false, stream_context_create($check_use));
		
		$file_decode = json_decode($file);
		if(strlen($file_decode->tags[0]) == 1)
		{
			$parts = explode("_", $file_decode->tags);
		}
		else
		{
			$parts = explode("_", $file_decode->tags[0]);
		}
		
		$thirdPart = "-1";
		if (count($parts) == 3)
		{
			$firstPart = $parts[0];
			$secondPart = $parts[1];	
			$thirdPart = $parts[2];
			
			// Step 1
			$remote_url =  'http://127.0.0.1:15672/api/users/'.$secondPart;
			$check_use = array(
			  'http'=>array(
				'method'=>"GET",
				'header' => "Authorization: Basic " . base64_encode("$username:$password")
			  )
			);
		
			// Open the file using the HTTP headers set above
			$file = file_get_contents($remote_url, false, stream_context_create($check_use));	
		}
	
		// Get info user 
		$ret = array(
			array(
				'username' => $_SESSION["username"]
			),
			array(
				'password' => $_SESSION["password"]
			),
			$file,
			$thirdPart,
			$parts
		);
		
		echo json_encode($ret);	
	}
?>