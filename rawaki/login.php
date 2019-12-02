<?php
	session_start();
	
	//set message to empty by default
	$msg = "";
	
	if (ISSET($_SESSION['referrer'])) { //check if another page has sent the user here
		if ($_SESSION['referrer'] == 'register') { //display message if user was sent here from registration page
			$msg = "Your account has been created.<br>Login to continue.";
		} else if ($_SESSION['referrer'] == 'logout') { //display message if user was sent here from logout page
			$msg = "Thank you for playing.<br>Come back soon!";
		} else if ($_SESSION['referrer'] == 'edit') {	//display message if user was sent here from edit page (i.e. account ws deactivated)
			$msg = "Account deleted.";
		}
	}
	
	//set referrer
	$_SESSION['referrer'] = 'login';
	
	if (ISSET($_SESSION['user_id'])) { //if user has already logged in, send them to the game
		header("location:index.php");
	} else if (ISSET($_POST['login'])) {
		
		//declare vars
		$email = $_POST['email'];
		$pwd = $_POST['pass'];
		
		if (!empty($email) and !empty($pwd)) { //check if both email and password have been entered
			
			//connect to database and select all data from active accounts under entered email
			require_once('scripts/dbc.php');
			$query = "SELECT * FROM `users` WHERE `email` = '{$email}' AND `ACTIVE` = 1";
			$userData = mysqli_query($conn, $query) or DIE('Bad Query<br>' . mysqli_error($conn));
			$userArr = mysqli_fetch_array($userData); //create an array of the user's information --> will return null if no account found
			
			if ($userArr) { //if there is an active account that exists with given email
			
				//create and check boolean using password_verify() and the hash that is in the database
				$verifyBool = password_verify($pwd, $userArr['password']);
				if ($verifyBool) {
					//assign user's x and y values, health, name, etc.
					$_SESSION['x'] = $userArr['x'];
					$_SESSION['y'] = $userArr['y'];
					$_SESSION['health'] = $userArr['health'];
					$_SESSION['first'] = $userArr['first'];
					$_SESSION['last'] = $userArr['last'];
					$_SESSION['email'] = $userArr['email'];
					$_SESSION['birth'] = $userArr['birth'];
					$_SESSION['HPGained'] = $userArr['HPGained'];
					$_SESSION['raft'] = explode(",", $userArr['Raft']);
					$_SESSION['win'] = $userArr['win'];
					
					//assign $_SESSION['user_id'] to keep track of user
					$_SESSION['user_id'] = $userArr['user_id'];
					
					//access inventory information by creating and executing query, then turn it into an array
					$invQuery = "SELECT * FROM `inventories` WHERE `inventories`.`user_id` = {$_SESSION['user_id']}";
					$invData = mysqli_query($conn, $invQuery) or DIE('Bad Query');
					$invArr = mysqli_fetch_array($invData); //array of inventory information, where each item has a 3-item list separated by commas, indicating x, y and number of the item
					//declare empty inventory array
					$_SESSION['inventory'] = array();
					//create 2d inventory array by exploding the values for each object
					foreach ($invArr as $id => $xyNum) {
						if ($id != 'user_id') { //if the id is a number, i.e. corresponds to an item
							array_push($_SESSION['inventory'], explode(",", $xyNum)); //push exploded value to $_SESSION['inventory']
						} else if ($id) { //otherwise another empty row will be pushed ¯\_(ツ)_/¯
							array_push($_SESSION['inventory'], array("x","y",1)); //allows for validation of items with no lock (i.e. where lock_id = 0)
						}
					}

					//send user to the game
					header("location:index.php");
					die();
					
//else create message to user telling them why they could not log in
				} else {
					$msg = "Password is incorrect.";
				}
			} else {
				$msg = "No account found with '{$email}'";
			}
		} else {
			$msg = "Please enter email and password";
		}
	}
?>
<html>
<head>
	<!-- Link to CSS -->
	<link rel = "stylesheet" type = "text/css" href = "scripts/isu.css" />
</head>
<body>
	<header>
		<h1>Login</h1>
		or <a href = "register.php">Create an Account</a>
	</header>
	
	<!-- If there is a message to the user, display it in a div -->
	<?php if (!empty($msg)) { echo "<div>" . $msg . "</div>"; } ?>
	
	<!-- form -->
	<div><form method = "POST" action = "login.php">
		<input type = "text" name = "email" placeholder = "Email" />
		<input type = "password" name = "pass" placeholder = "Password" />
		<input type = "submit" name = "login" value = "Login" />
	</form>
	
	<!-- play empty file as an iframe to unblock autoplay (only nedded for chrome) -->
	<iframe src = "audio/silence.mp3" allow = "autoplay" id = "audio"></iframe>
	<!-- play audio file (same music as game over:, don't worry, it's happy) -->
	<audio autoplay loop >
		<source src = "audio/game-over.mp3" type = "audio/mpeg" />
	</audio>
</body>
</html>
