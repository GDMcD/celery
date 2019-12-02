<?php
	session_start();
	
	//redirect if already logged in
	if (ISSET($_SESSION['user_id'])) {
		header("location:index.php");
		die();
	}
	
	//set msg to empty by default
	$msg = "";
	
	//check if form is submitted and set vars
	if (ISSET($_POST['sub'])) {
		$first = $_POST['first'];
		$last = $_POST['last'];
		$email = $_POST['email'];
		$birth = $_POST['birth'];
		$pass = $_POST['pass'];
		$confirm = $_POST['confirm'];
		
		//set min and max birth dates
		$min = date_create();
		date_sub($min, date_interval_create_from_date_string('100 years')); //minimum birth date of 100 years ago today
		$max = date_create();
		date_sub($max, date_interval_create_from_date_string('8 years')); 
		
		//select `ACTIVE` field from users table at the provided email address
		$select = "SELECT `ACTIVE` FROM `users` WHERE `users`.`email` = '{$email}'"; //maximum birth date of 8 years ago today
		require_once('scripts/dbc.php');
		$emailCheck = mysqli_query($conn, $select);
		
		//check if data meets requirements:
		if (!empty($first) and !empty($last) and !empty($email) and !empty($birth) and !empty($pass) and !empty($confirm)) { //all fields filled
			if (!preg_match("/[0-9]/", $first) or !preg_match("/[0-9]/", $last)) { //names do not contain numbers
				if ($birth > date_format($min, 'Y-m-d') and $birth < date_format($max, 'Y-m-d')) { //Between 8 and 100 yrs old
					if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) { //email follows valid format
						//create array of `ACTIVE` fields for the email address --> will return NULL if there is no existing account
						$data = mysqli_fetch_array($emailCheck);
						if (!$data) { //if data retured NULL (i.e. no account exists)
							if ($pass == $confirm) { //check that passwords match
								if (strlen($pass) >= 8) { //check that password is at least 8 characters long
									//create hash of password to protect data in database
									$hash = password_hash($pass, PASSWORD_DEFAULT);
									
									//create row in users table for the user, with first name, last name, email, date of birth and hashed hashed password (as well as default location and health)
									$query = "INSERT INTO `users` (`user_id`, `first`, `last`, `email`, `birth`, `password`) VALUES (NULL, '{$first}', '{$last}', '{$email}', '{$birth}', '{$hash}')";
									mysqli_query($conn, $query) or DIE ('Bad Query<br>' . mysqli_error($conn));
									
									//create an inventory for the user. All other values are default
									$inventoryQuery = "INSERT INTO `inventories` (`user_id`) VALUES (NULL)";
									mysqli_query($conn, $inventoryQuery) or DIE('Bad Query');
									
									//send to login page with $_SESSION['referrer'] indicating to login that a user has recently registered
									$_SESSION['referrer'] = 'register';
									header("location:login.php");
									die();
								} else {
									$msg = "Password must be 8 characters or longer."; //notify user why their data did not meet requirements
								}
							} else {
								$msg = "passwords do not match.";
							}
						} else { //if data was found
							if ($data['ACTIVE']) { //the ACTIVE field is a boolean. Value of 1 indicates current account
								$msg = "Oops, looks like there is already an account under that email!";
							} else { //Account exists but was deactivated
								//create hash of password to protect data in database
								$hash = password_hash($pass, PASSWORD_DEFAULT);
								
								//reactivate account and update password, first, last and birth date (inventory, health and location was already reset upon deactivation)
								$activate = "UPDATE `users` SET `first` = '{$first}', `last` = '{$last}', `birth` = '{$birth}', `password` = '{$hash}', `ACTIVE` = 1 WHERE `users`.`email` = '{$email}'";
								mysqli_query($conn, $activate) or DIE ('Bad Query<br>' . mysqli_error($conn));
								
								//send to login page with $_SESSION['referrer'] indicating to login that a user has recently registered
								$_SESSION['referrer'] = 'register';
								header("location:login.php");
								die();
							}
						}
//notify user why their data did not meet requirements
					} else {
						$msg = "Email is invalid.";
					}
				} else {
					$msg = "You must be older than 8 and younger than 100 to play this game.";
				}
			} else {
				$msg = "First and/or last names must not contain numbers.";
			}
		} else {
			$msg = "All fields must be filled.";
		}
	}
?>
<html>
<head>
	<!-- Link to CSS -->
	<link rel = "stylesheet" type = "text/css" href = "scripts/isu.css" />
<body>
	<header>
		<h1>Create an Account</h1>
		or <a href = "login.php">login</a>
	</header>

	<!-- if there is a message to display, display it in a div -->
	<?php if (!empty($msg)) { echo "<div>" . $msg . "</div>"; } ?>

	<!-- form -->
	<div><form method = "POST" action = "register.php">
		<input type = "text" name = "first" placeholder = "First Name" />
		<input type = "text" name = "last" placeholder = "Last Name" />
		<input type = "text" name = "email" placeholder = "Email" />
		<input type = "text" name = "birth" placeholder = "Date of Birth" onfocus = "(this.type='date')" onblur="(this.type='text')" 
			min = "<?php echo date_format($min, 'Y-m-d'); ?>"
			max = "<?php echo date_format($max, 'Y-m-d'); ?>" />
		<input type = "password" name = "pass" placeholder = "Password" />
		<input type = "password" name = "confirm" placeholder = "Confirm Password"/>
		<input type = "submit" name = "sub" value = "Register" />
	</form></div>
	
	<!-- play empty file as an iframe to unblock autoplay (only nedded for chrome) -->
	<iframe src = "audio/silence.mp3" allow = "autoplay" id = "audio"></iframe>
	<!-- play audio file (same music as game over:, don't worry, it's happy) -->
	<audio autoplay loop >
		<source src = "audio/game-over.mp3" type = "audio/mpeg" />
	</audio>
</body>
</html>
