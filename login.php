<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);
	//directory where images are saved
	$dir = "gallery/";

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
	$id = isset($_POST["userid"]);
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false

?>

<!DOCTYPE html>
<html>

<head>
    <title>IMY 220 - Assignment 2</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css" />
    <meta charset="utf-8" />
    <meta name="author" content="Johannes Wolmarans">
    <!-- Replace Name Surname with your name and surname -->
</head>

<body>
    <div class="container">
        <?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					$uid = $row['user_id'];
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";

					echo 	"<form action='login.php' method='post' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple/><br/>
									<input type='submit' class='btn btn-dark' value='Upload Image' name='submit' />
									<input id='loginEmail' name='loginEmail' type='hidden' value='".$email."'>
									<input id='loginPass' name='loginPass' type='hidden' value='".$pass."'>
									<input id='userid' name='userid' type='hidden' value='".$uid."'>
								</div>
							  </form>
							  <h1>Uploaded Images</h1>";
		if(isset($_FILES['picToUpload'])){
			$numfiles = count($_FILES['picToUpload']['name']);
			for ($i=0;$i<$numfiles;$i++){
				$pic = $_FILES['picToUpload']['tmp_name'][$i];
				$picname =$_FILES['picToUpload']['name'][$i];
				$size = filesize($pic);
				$type = strtolower(pathinfo($picname, PATHINFO_EXTENSION));//retrieve file type
				if (($type == "jpg" || $type == "jpeg" ) && $size < 1048576){ //check file type && size constraints given
					move_uploaded_file($pic,$dir.$picname);
					$sql = "INSERT INTO `tbgallery`(`user_id`, `filename`) VALUES ('$uid','$picname')";
					$mysqli->query($sql);
				} else if (!($type == "jpg" || $type == "jpeg" )){
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							'.$picname.' not uploaded, because it is not a .jpg file.
							</div>';
				} else {
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							'.$picname.' not uploaded, because it is larger than 1MB.
							</div>';
				}
			}
					}
					$get = "SELECT * FROM tbgallery WHERE user_id = $uid";
					$result = $mysqli->query($get);
					if ($result->num_rows > 0){
						echo '<div class="row imageGallery">';
						while($pics = $result->fetch_assoc()) {
							echo '<div class="col-4" style="background-image: url('.$dir.$pics["filename"].'")>
								</div>';
						};
						echo '</div>';
					} else {
						echo '<div class="alert alert-primary mt-3" role="alert">
	  							User has not uploaded any images yet.
	  						</div>';
					}
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			}
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
    </div>
</body>

</html>