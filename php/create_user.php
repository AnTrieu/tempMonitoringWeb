<?php 
session_start();

$username = getenv('USER_DEFAULT');
$password = getenv('PASS_DEFAULT');

// Step 1
$remote_url = 'http://127.0.0.1:15672/api/users/'.$_POST['user_input'];
$check_use = array(
  'http'=>array(
    'method'=>"GET",
    'header' => "Authorization: Basic " . base64_encode("$username:$password")
  )
);

// Open the file using the HTTP headers set above
$file = file_get_contents($remote_url, false, stream_context_create($check_use));

if(strlen($file) > 0)
{
	echo "error exist";
	return;
}

// Create a stream to create user
if(strcmp($_POST['type_input'],"administrator") == 0)
{
	$tags = "administrator";
}
else if(strcmp($_POST['type_input'],"Leader") == 0)
{
	$tags = 'Active_'.$_POST['type_input']."_0000-00-00_".$_POST['size_input'];
}
else
{
	$tags = "Member_".$_POST['leader_input']."_".$_POST['permission'];
}
$postdata = json_encode(
    array(
        'password' => $_POST['pass_input'],
        'tags' => $tags
    )
);
$create_use = array(
  'http'=>array(
    'method'=>"PUT",
    'header' => "Authorization: Basic " . base64_encode("$username:$password"),
    'content' => $postdata
  )
);

// Open the file using the HTTP headers set above
$file = file_get_contents($remote_url, false, stream_context_create($create_use));

// Step 2
$vhost_url = 'http://127.0.0.1:15672/api/permissions/%2F/'.$_POST['user_input'];
$postdata = json_encode(
    array(
        'configure' => '.*',
        'write' => '.*',
		'read' => '.*'
    )
);

$set_vhost = array(
  'http'=>array(
    'method'=>"PUT",
    'header' => "Authorization: Basic " . base64_encode("$username:$password"),
    'content' => $postdata
  )
);

// Open the file using the HTTP headers set above
$file = file_get_contents($vhost_url, false, stream_context_create($set_vhost));

// Step 3
$remote_url = 'http://127.0.0.1:15672/api/users/'.$_POST['user_input'];
// Create a stream to check user exist
$check_use = array(
  'http'=>array(
    'method'=>"GET",
    'header' => "Authorization: Basic " . base64_encode("$username:$password")
  )
);

// Open the file using the HTTP headers set above
$file = file_get_contents($remote_url, false, stream_context_create($check_use));

if(strlen($file) > 0)
{
	$video_folder = $_SERVER['DOCUMENT_ROOT'].'/database/'.$_POST['user_input'].'/videos/'.$_POST['user_input'];
	if (!file_exists($video_folder)) 
	{
		mkdir($video_folder, 0777, true);
	}
	
	$program_folder = $_SERVER['DOCUMENT_ROOT'].'/database/'.$_POST['user_input'].'/programs/'.$_POST['user_input'];
	if (!file_exists($program_folder)) 
	{
		mkdir($program_folder, 0777, true);
		// Thiết lập quyền truy cập đầy đủ cho thư mục
		chmod($program_folder, 0777);			
	}
			
	$device_folder = $_SERVER['DOCUMENT_ROOT'].'/database/'.$_POST['user_input'].'/devices/';
	if (!file_exists($device_folder)) 
	{
		mkdir($device_folder, 0777, true);
		// Thiết lập quyền truy cập đầy đủ cho thư mục
		chmod($device_folder, 0777);			
	}	
	$logging_folder = $_SERVER['DOCUMENT_ROOT'].'/database/'.$_POST['user_input'].'/logs/';
	if (!file_exists($logging_folder)) 
	{
		mkdir($logging_folder.'account', 0777, true);
		mkdir($logging_folder.'device', 0777, true);
		
		// Thiết lập quyền truy cập đầy đủ cho thư mục
		chmod($device_folder.'account', 0777);	
		// Thiết lập quyền truy cập đầy đủ cho thư mục
		chmod($device_folder.'device', 0777);			
	}	

	if(strstr($tags,"Member_") !== false)
	{
		if (file_exists($video_folder) && is_dir($video_folder))
		{
			rmdir($video_folder);
		}		
		if (file_exists($program_folder) && is_dir($program_folder))
		{
			rmdir($program_folder);
		}		
		
		$video_folder_src = $_SERVER['DOCUMENT_ROOT'].'/database/'.$_POST['leader_input'].'/videos/'.$_POST['leader_input'];
		$program_folder_src = $_SERVER['DOCUMENT_ROOT'].'/database/'.$_POST['leader_input'].'/programs/'.$_POST['leader_input'];
		symlink($video_folder_src, $video_folder);
		symlink($program_folder_src, $program_folder);
	}
	
	echo "ok";
}
else
{
	echo "error";
}
?>