<?php 
 
//importing dbDetails file
require_once 'dbDetails.php';

//JUST FOR DEBUG
//error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
//ini_set('display_errors', '1');

	 
//this is our upload folder 
$upload_path = 'images/';
  
//Getting the server ip 
$server_ip = gethostbyname(gethostname());
 
//creating the upload url 
$upload_url = 'http://'.$server_ip.'/'.$upload_path; 
 
//response array 
$response = array(); 
 
if($_SERVER['REQUEST_METHOD']=='GET'){
	print('alo');
	#$con = mysqli_connect($HOST,$USER,$PASS,$DB);
	$con = mysqli_connect("172.28.0.2",USER,PASS,DB) or die('Unable to Connect...');
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
	//checking the required parameters from the request 
	if(isset($_POST['name']) and isset($_FILES['image']['name'])){
	 	#print('alo');
	 
		//connecting to the database 
		#$con = mysqli_connect($HOST,$USER,$PASS,$DB);
		$con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect...');
		#$con = mysqli_connect("localhost", "o", "o", "carsides");

		// Check connection
		if (mysqli_connect_errno($con)){
			  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
			    exit();
		}
	  
		//getting name from the request 
		$name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	 
		//getting file info from the request 
		$fileinfo = pathinfo($_FILES['image']['name']);
	 
		//getting the file extension 
		$extension = filter_var($fileinfo['extension'], FILTER_SANITIZE_SPECIAL_CHARS);
		 
		//file url to store in the database 
		$file_url = $upload_url . getFileName() . '.' . $extension;
			 
		//file path to upload in the server 
		$file_path = $upload_path . getFileName() . '.'. $extension; 
				 
		//trying to save the file in the directory 
		try{
			//saving the file 
			move_uploaded_file($_FILES['image']['tmp_name'],$file_path);
			$sql = "INSERT INTO `carsides`.`images` (`id`, `url`, `name`) VALUES (NULL, '$file_url', '$name');";
			 
			//adding the path and name to database 
			if(mysqli_query($con,$sql)){
					 
				//filling response array with values 
				$response['error'] = "null"; 
				#$response['url'] = $file_url; 
				#$response['name'] = $name;
			}
		//if some error occurred 
		}catch(Exception $e){
			$response['error']=true;
			$response['message']=$e->getMessage();
		} 

		//displaying the response 
		echo json_encode($response);
		//closing the connection 
		mysqli_close($con);

	}else{
		$response['error']=true;
		$response['message']='Please choose a file';
	}
}


/*
We are generating the file name 
so this method will return a file name for the image to be upload 
*/
function getFileName(){
	$con = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect...');
	$sql = "SELECT max(id) as id FROM images";
	$result = mysqli_fetch_array(mysqli_query($con,$sql));
 	 
	mysqli_close($con);
	if($result['id']==null)
		return 1; 
	else 
		return ++$result['id']; 
}
