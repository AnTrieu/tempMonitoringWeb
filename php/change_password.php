<?php
	session_start();

	if(!empty($_POST["username"]) && !empty($_POST["password_old"]) && !empty($_POST["password_new"]))
	{
		
		// Get info account
		$username = getenv('USER_DEFAULT');
		$password = getenv('PASS_DEFAULT');
		$username_enter = $_POST["username"];
		$password_old_enter = $_POST["password_old"];
		$password_new_enter = $_POST["password_new"];
		
		// Step 1 kiem tra thông tin login
		$remote_url = 'http://127.0.0.1:15672/api/whoami';
		$check_use = array(
			'http'=>array(
				'method'=>'GET',
				'header' => array(
					'Content-Type: application/json\r\n',
					"Authorization: Basic ". base64_encode("$username_enter:$password_old_enter"),
					"Connection: keep-alive"
				)
			)
		);
		
		$context = stream_context_create($check_use);
		$file = file_get_contents($remote_url, false, $context);
		$headers = $http_response_header;
		
		if((strpos($headers[0],"Unauthorized") !== false) && (strpos($headers[2],"50") !== false))
		{
			echo 'error info';
			return;
		}
		else
		{
			// Step 2
			$remote_url = 'http://'.getenv('HOST_IP').':15672/api/users/'.$username_enter;
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
									'password' => $password_new_enter,
									'tags' => $file_decode->tags
								)
							)
			  )
			);	

			// Open the file using the HTTP headers set above
			$file = file_get_contents($remote_url, false, stream_context_create($update_use));	

			echo $file;
		}	
	}
	else
	{
		echo "error";
	}			
?>