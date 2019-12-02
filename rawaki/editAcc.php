<?php
	session_start();
	
	//set message empty by default
	$msg = "";
	
	//check if user has logged in
	if (!ISSET($_SESSION['user_id'])) {
		header("location:login.php"); //if not, send to login
		die();
	}
	
	//create min and max dates which can be entered as birth dates
	$min = date_create();
	date_sub($min, date_interval_create_from_date_string('100 years')); //minimum year is 100 years ago today
	$max = date_create();
	date_sub($max, date_interval_create_from_date_string('8 years'));  //maximum year is 8 years ago today
	
	require_once("scripts/dbc.php"); //connect to db
	
	//First name
	if (!empty($_POST['first']) and $_POST['first'] != $_SESSION['first']) { //check if field is filled and if any changes have been made
		if (!preg_match("/[0-9]/", $_POST['first'])) { //make sure there are no numbers in the name
		
			//create and execute query to update first name
			$firstQ = "UPDATE `users` SET `first` = '{$_POST['first']}' WHERE `users`.`user_id` = {$_SESSION['user_id']}";
			mysqli_query($conn, $firstQ) or DIE('Bad Query --> First');
			
			//notify user about update and set array value to new value so that it shows up in placeholder
			$msg .= "First Name Updated.<br>";
			$_SESSION['first'] = $_POST['first'];
			
		} else {
			$msg .= "First name must not contain numbers.<br>"; //if name was invalid, notify user
		}
	}
	//Last name
	if (!empty($_POST['last'])and $_POST['last'] != $_SESSION['last']) { //check if field is filled and if any changes have been made
		if (!preg_match("/[0-9]/", $_POST['last'])) { //make sure there are no numbers in the name
		
			//create and execute query to update last name
			$lastQ = "UPDATE `users` SET `last` = '{$_POST['last']}' WHERE `users`.`user_id` = {$_SESSION['user_id']}";
			mysqli_query($conn, $lastQ) or DIE('Bad Query --> Last');
			
			//notify user about update and set array value to new value so that it shows up in placeholder
			$msg .= "Last Name Updated.<br>";
			$_SESSION['last'] = $_POST['last'];
			
		} else {
			$msg .= "Last name must not contain numbers.<br>"; //if name was invalid, notify user
		}
	}
	//Email
	if (!empty($_POST['email'])and $_POST['email'] != $_SESSION['email']) { //check if field is filled and if any changes have been made
		if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $_POST['email'])) { //make sure email follows valid format
			
			//create and execute query, get result for other emails that match email entered
			$otherEmails = "SELECT * FROM `users` WHERE `email` = '{$_POST['email']}'";
			$emailData = mysqli_query($conn, $otherEmails) or DIE('Bad Query --> Other Emails');
			$emailArr = mysqli_fetch_array($emailData); //should return null if no other accounts have same email
			
			if (!$emailArr) { //i.e. if no other accounts were found with entered email
				
				//create and execute query to update email
				$emailQ = "UPDATE `users` SET `email` = '{$_POST['email']}' WHERE `users`.`user_id` = {$_SESSION['user_id']}";
				mysqli_query($conn, $emailQ) or DIE('Bad Query --> Email');
				
				//notify user about update and set array value to new value so that it shows up in placeholder
				$msg .= "Email Updated.<br>";
				$_SESSION['email'] = $_POST['email'];
				
			} else { //i.e. there is already an account under desired email
				$msg .= "There is already an account with '{$_POST['email']}'"; //notify user
			}
		} else {
			$msg .= "Invalid email.<br>"; //if email was invalid, notify user
		}
	}
	//Birth date
	if (!empty($_POST['birth']) and $_POST['birth'] != $_SESSION['birth']) { //check if field is filled and if any changes have been made
		if ($_POST['birth'] > date_format($min, 'Y-m-d') and $_POST['birth'] < date_format($max, 'Y-m-d')) { //make sure birth date is within allowed range
			
			//create and execute query to update email
			$birthQ = "UPDATE `users` SET `birth` = '{$_POST['birth']}' WHERE `users`.`user_id` = {$_SESSION['user_id']}";
			mysqli_query($conn, $birthQ) or DIE('Bad Query --> Birth');
			
			//notify user about update and set array value to new value so that it shows up in placeholder
			$msg .= "Birth Date Updated.<br>";
			$_SESSION['birth'] = $_POST['birth'];
			
		} else {
			$msg .= "You must be older than 8 and younger than 100 to play this game.<br>"; //notify user if birth date is out of range
		}
	}
	//Password
	if (!empty ($_POST['pass'])) { //check if field is filled
		if (!empty ($_POST['confirm'])) { //check if password was confirmed
			if ($_POST['pass'] == $_POST['confirm']) { //check if passwords match
				if (strlen($_POST['pass']) >= 8) { //check that password is of acceptable length
					
					//create hash, create query with hash and execute query
					$hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
					$passQ = "UPDATE `users` SET `password` = '{$hash}' WHERE `users`.`user_id` = {$_SESSION['user_id']}";
					mysqli_query($conn, $passQ) or DIE('Bad Query --> Pass');
					
					$msg .= "Password Updated.<br>"; //notify user
					
//else notify user why password was not acceptable
				} else {
					$msg .= "Password must be 8 characters or longer.<br>";
				}
			} else {
				$msg .= "Paswords do not match.<br>";
			}
		} else {
			$msg .= "Please Confirm Password.<br>";
		}
	}
	
	//profile icon
	if (ISSET($_POST['imgSub'])) {
		$imgName = basename($_FILES['file']['name']); //current image name
		$imgExtension = strtolower(pathinfo($imgName,PATHINFO_EXTENSION)); //get file extension
		
		$upload = true; //by default let upload go ahead
		
		$newName = "userImgs/" . $_SESSION['user_id'] . ".png"; //make new name and convert file to png (will work as long as file is accepted format below)
		
		// Check if image file is an actual image
		if (!empty($_FILES['file']['tmp_name'])) {
			$check = getimagesize($_FILES['file']['tmp_name']);
			if ($check == false) {
				$msg .= "File is not an image.<br>";
				$upload = false;
			}
		} else {
			$msg .= "No file selected.<br>";
			$upload = false;
		}

		// Check file size (none bigger than 1 MB
		if ($_FILES['file']['size'] > 1000000) {
			$msg .= "Image file must be less than 1 megabyte.<br>";
			$upload = false;
		}
		
		// Only allow common image files
		if($imgExtension != "jpg" && $imgExtension != "png" && $imgExtension != "jpeg"
		&& $imgExtension != "gif" ) {
			$msg .= "Only JPG, JPEG, PNG and GIF files are allowed.<br>";
			$upload = false;
		}
		
		// Check if $upload is set to false by an error
		if ($upload == false) {
			$msg .= "<br>Your file was not uploaded.";
		// else try to upload file
		} else {
			if (move_uploaded_file($_FILES['file']['tmp_name'], $newName)) {
				$msg = "Your file has been uploaded.";
			} else {
				$msg = "Sorry, there was an error uploading your file."; //tell them if there was an error
			}
		}
	}
	
	//Delete (deactivate) Account
	if (ISSET($_POST['del']) and $_POST['del'] == "delete") { //check that delete button has been pressed and confirmed
	
		//create and execute queries to reset game-based values (i.e. health, location, inventory) and set ACTIVE status to 0
		$delete = "UPDATE `users` SET `ACTIVE` = 0, `x` = DEFAULT, `y` = DEFAULT, `health` = DEFAULT, `HPGained` = DEFAULT, `raft` = DEFAULT, `win` = DEFAULT WHERE `users`.`user_id` = {$_SESSION['user_id']}";
		$invReset = "UPDATE `inventories` SET `1`= DEFAULT,`2`= DEFAULT,`3`= DEFAULT,`4`= DEFAULT,`5`= DEFAULT,`6`= DEFAULT,`7`= DEFAULT,`8`= DEFAULT,`9`= DEFAULT,`10`= DEFAULT,`11`= DEFAULT,`12`= DEFAULT,`13`= DEFAULT, `14` = DEFAULT, `15` = DEFAULT WHERE `inventories`.`user_id` = {$_SESSION['user_id']}";
		mysqli_query($conn, $delete) or DIE('Bad Delete');
		mysqli_query($conn, $invReset) or DIE('Bad Inventory Reset');
		
		session_destroy(); //destroy current session
		
		//start new session to preserve referral data and send user to login
		session_start();
		$_SESSION['referrer'] = 'edit';
		header("location:login.php");
		die();
	}
?>
<html>
<head>
	<!-- Link to CSS -->
	<link rel = "stylesheet" type = "text/css" href = "scripts/isu.css" />
</head>
<body>
	<header>
		<h1>Edit Your Account</h1>
		or <a href = "index.php">back to game</a>
	</header>
	
	<!-- If there is a message to the user, display it in a div -->
	<?php if (!empty($msg)) { echo "<div>" . $msg . "</div>"; } ?>
	
	<!-- user icon form -->
	<div><form action = "editAcc.php" method = "POST" enctype = "multipart/form-data">
		<label for = "file">Choose Profile Icon</label>
		<input type = "file" name = "file" id = "file" /></label>
		<input type = "submit" name = "imgSub" id = "imgSub" />
	</form>
		
	<!-- user information form -->
	<form method = "POST" action = "editAcc.php">
		<input type = "text" name = "first" placeholder = "First Name" onfocus = "(value = '<?php echo $_SESSION['first'] ?>')"/>
		<input type = "text" name = "last" placeholder = "Last Name" onfocus = "(value = '<?php echo $_SESSION['last'] ?>')" />
		<input type = "text" name = "email" placeholder = "Email" onfocus = "(value = '<?php echo $_SESSION['email'] ?>')" />
		<input type = "text" name = "birth" placeholder = "Date of Birth" onfocus = "(this.type='date')(value = '<?php echo $_SESSION['birth'] ?>')" onblur="(this.type='text')"
			min = "<?php echo date_format($min, 'Y-m-d'); ?>"
			max = "<?php echo date_format($max, 'Y-m-d'); ?>" />
		<input type = "password" name = "pass" placeholder = "Password" />
		<input type = "password" name = "confirm" placeholder = "Confirm Password"/>
		<input type = "submit" name = "sub" value = "Save Changes" />
	</form>
	
	<span class = "subheading">OR</span>
	
	<!-- delete account form -->
	<form method = "POST" action = "editAcc.php">
		<input type = "hidden" id = "del" name = "del" value = "0" /> <!-- value is equal to zero unless confirmed -->
		<input type = "submit" onclick = "confirmButton()" value = "Delete Account" /> <!-- pressing button will activate confirmButton() function -->
	</form>
	
	<script>
		function confirmButton() {
			var val; //declare empty var
			var bool = confirm("Are you sure you want to deactivate your account?"); //message which appears in confirm dialog
			if (bool == true) { //if user confirms, set val to delete
				val = "delete";
			} else { //else set val to 0
				val = 0;
			}
			document.getElementById("del").value = val; //set $_POST['del'] equal to val
		}
	</script>
	
	<!-- play empty file as an iframe to unblock autoplay (only nedded for chrome) -->
	<iframe src = "audio/silence.mp3" allow = "autoplay" id = "audio"></iframe>
	<!-- play audio file (same music as game over:, don't worry, it's happy) -->
	<audio autoplay loop >
		<source src = "audio/game-over.mp3" type = "audio/mpeg" />
	</audio>
</body>
</html>
