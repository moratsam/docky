<?php 
 
//importing dbDetails file
require_once 'dbDetails.php';

//JUST FOR DEBUG
//error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
//ini_set('display_errors', '1');

  
//Getting the server ip 
$server_ip = gethostbyname(gethostname());
//creating the upload url 
$upload_url = 'http://'.$server_ip.'/'.$upload_path; 
$upload_path = 'images/';

	 
//response array 
$response = array(); 
 
//this is just for testing desu
if($_SERVER['REQUEST_METHOD']=='GET'){
	print('alo');
	#$con = mysqli_connect($HOST,$USER,$PASS,$DB);
	#$con = mysqli_connect("172.28.0.2",USER,PASS,DB) or die('Unable to Connect...');
	$con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect...');
	#$con = mysqli_connect("localhost", "o", "o", "carsides");

	// Check connection
	if (mysqli_connect_errno($con)){
		  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		    exit();
	}
	print('alo');
	$sql = "SELECT * FROM `carsides`.`images`;";
			 
	//adding the path and name to database 
	$res = mysqli_query($con,$sql);
	if ($res){
		while ($row = mysqli_fetch_row($res)){
			print($row[0] . "\n");
		}
	}
	print('neger');
	print('neger');
}
  

if($_SERVER['REQUEST_METHOD']=='POST'){
	//getting name from the request 
	$postName = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);

	//checking the required parameters from the request 
	if(isset($_POST['name']) and isset($_FILES['image']['name']) and isNameViable()){
	 	#print('alo');
	 
		//connecting to the database 
		$con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect...');

		// Check connection
		if (mysqli_connect_errno($con)){
			echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
			exit();
		}
	  
		//getting the file extension 
		$fileinfo = pathinfo($_FILES['image']['name']);
		$extension = filter_var($fileinfo['extension'], FILTER_SANITIZE_SPECIAL_CHARS);
		 
		$newFilename = genFilename();
		//file url which will get inserted into db
		$file_url = $upload_url . $newFilename . '.' . $extension;
		//server path to which file will be uploaded
		$file_path = $upload_path . $newFilename . '.'. $extension; 
				 
		try{
			//saving the file to server
			move_uploaded_file($_FILES['image']['tmp_name'], $file_path);

			//insert into db
			$sql = "INSERT INTO `carsides`.`images` (`id`, `url`, `name`) VALUES (NULL, '$file_url', '$postName');";

			//no error; create response
			if(mysqli_query($con,$sql)){
				$response['error'] = "null"; 
			}
		//if some error occurred 
		}catch(Exception $e){
			$response['error']=true;
			$response['message']=$e->getMessage();
		} 

		//send response 
		echo json_encode($response);
		//close the connection 
		mysqli_close($con);

	}else{
		$response['error']=true;
		$response['message']='Please choose a correctly named file';
		//send response 
		echo json_encode($response);
	}
}

//check if prefix defines viable label
function isNameViable(){
	$postName = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	if(strlen($postName) < 3){
		return false;
	}

	$f = $postName[0]; #first letter is predicted class
	$s = $postName[1]; #second letter is actual class
	#third letter is '_'
	if(($f=='B' || $f=='D' || $f=='F' || $f=='L' || $f=='R') && 
		($s=='B' || $s=='D' || $s=='F' || $s=='L' || $s=='R') &&
		$postName[2]=='_'){
			return true;
	} else{
		return false;
	}
}

//retrieve max id of image from db; increment it
//return filename as XX_maxID, where XX is viable label prefix
function genFilename(){
	$postName = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);

	$con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect...');
	$sql = "SELECT max(id) as id FROM images";
	$result = mysqli_fetch_array(mysqli_query($con,$sql));
	mysqli_close($con);

	$maxID = 0;
	if($result['id']==null)
		$maxID = 1; 
	else 
		$maxID =  $result['id']++; 

	return substr($postName, 0, 3) . $maxID . "_" . time();
}
