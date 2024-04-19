<?php 
session_start();

function rrmdir($dir)
{
	if (is_dir($dir))
	{
		
		$objects = scandir($dir);
		
		foreach ($objects as $object)
		{
			if ($object != '.' && $object != '..')
			{			
				if (filetype($dir.'/'.$object) == 'dir') {rrmdir($dir.'/'.$object);}
				else 
				{
					unlink($dir.'/'.$object);
				}
			}
		}
			
		reset($objects);
		rmdir($dir);
	}
}

$username = getenv('USER_DEFAULT');
$password = getenv('PASS_DEFAULT');

for ($x = 0; $x < count($_POST['users']); $x++) 
{
	// Step 1
	$remote_url = 'http://127.0.0.1:15672/api/users/'.$_POST['users'][$x];
	$check_use = array(
	  'http'=>array(
		'method'=>"GET",
		'header' => "Authorization: Basic " . base64_encode("$username:$password")
	  )
	);

	// Open the file using the HTTP headers set above
	$file = file_get_contents($remote_url, false, stream_context_create($check_use));

	$file_decode = json_decode($file);
	$parts = explode("_", $file_decode->tags[0]);
		
	// List device Leader
	$UUID = [];
	$devices = $_SERVER['DOCUMENT_ROOT'].'/database/'.$_POST['users'][$x].'/devices';
	if (count($parts) == 2)
	{
		$devices = $_SERVER['DOCUMENT_ROOT'].'/database/'.$parts[1].'/devices';	
	}
	$directories = array_diff(scandir($devices), array('..', '.'));
    foreach ($directories as $directory) {
        if (is_dir($devices . '/' . $directory)) {
            array_push($UUID, $directory);
        }
    }
		
	// Delete device of user
	for ($y = 0; $y < count($UUID); $y++)
	{	
		$fileDetail = $_SERVER['DOCUMENT_ROOT'].'/database/.Devices/'.$UUID[$y]."/info.txt";
		
		$myfile = fopen($fileDetail, "r");
		$contents = fread($myfile, filesize($fileDetail));
		fclose($myfile);

		$leader = substr($contents,0 ,strpos($contents,"\r")); $contents = substr($contents,strpos($contents,"\r") + 1);
		$member = substr($contents,0 ,strpos($contents,"\r")); $contents = substr($contents,strpos($contents,"\r") + 1);

		if(strcmp($member,$_POST['users'][$x]) == 0)
		{
			// Add in .Devices folder
			$myfile = fopen($fileDetail, "w")  or die("Unable to open file!");
			fwrite($myfile, $leader);
			fwrite($myfile, "\r");
			fclose($myfile);				
		}
		else if(strcmp($leader,$_POST['users'][$x]) == 0)
		{
			$fileDetail = $_SERVER['DOCUMENT_ROOT'].'/database/.Devices/'.$UUID[$y];
			rrmdir($fileDetail);
		}
	}
	
	// Delete folder user
	$file = $_SERVER['DOCUMENT_ROOT'].'/database/'.$_POST['users'][$x];	
	if (file_exists($file))
	{
		rrmdir($file);
	}
}

$remote_url = 'http://127.0.0.1:15672/api/users/bulk-delete';
$postdata = json_encode(
    array('users' => $_POST['users'])
);
$delete_use = array(
  'http'=>array(
    'method'=>"POST",
    'header' => "Authorization: Basic " . base64_encode("$username:$password"),
    'content' => $postdata
  )
);

// Open the file using the HTTP headers set above
$file = file_get_contents($remote_url, false, stream_context_create($delete_use));

echo 'ok';
?>