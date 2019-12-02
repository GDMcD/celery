<?php
	session_start();

	//if user has not logged in, send them to the login page
	if (!ISSET($_SESSION['user_id'])) {
		header("location:login.php");
		die();
	#NOTE: !ISSET referrer also fixes issue where $_SESSION values from OTHER games are accidentally transferred because of same index names
	} else if (!ISSET($_SESSION['referrer'])) { // if a refferal page is not set
		header("location:logout.php"); //log them out
		die();
	} else {

		if ($_SESSION['referrer'] == 'login') { //if user just logged in
			$welcome = true; //set welcome bool to true
		} else if ($_SESSION['referrer'] == 'dead' or ISSET($_POST['win']) or ISSET($_GET['reset'])) { //else if the user just FULLY died (game over), or they WON, or they clicked RESET
		
			//create and execute queries to reset inventory and user information
			require_once("scripts/dbc.php");
			$usrReset = "UPDATE `users` SET `x` = DEFAULT, `y` = DEFAULT, `health` = DEFAULT, `HPGained` = DEFAULT, `raft` = DEFAULT, `win` = DEFAULT WHERE `users`.`user_id` = {$_SESSION['user_id']}";
			$invReset = "UPDATE `inventories` SET `1`= DEFAULT,`2`= DEFAULT,`3`= DEFAULT,`4`= DEFAULT,`5`= DEFAULT,`6`= DEFAULT,`7`= DEFAULT,`8`= DEFAULT,`9`= DEFAULT,`10`= DEFAULT,`11`= DEFAULT,`12`= DEFAULT,`13`= DEFAULT, `14` = DEFAULT, `15` = DEFAULT WHERE `inventories`.`user_id` = {$_SESSION['user_id']}";
			mysqli_query($conn, $usrReset) or DIE('Bad User Reset');
			mysqli_query($conn, $invReset) or DIE('Bad Inventory Reset');
			
			//reset progress in game
			$_SESSION['x'] = 2;
			$_SESSION['y'] = 2;
			$_SESSION['HPGained'] = 0;
			$_SESSION['health'] = 3;
			$_SESSION['raft'] = array(0,0,0,0);
			$_SESSION['win'] = 0;
			
			//repeat inventory proccess (from login):
			require_once("scripts/dbc.php");
			$invQuery = "SELECT * FROM `inventories` WHERE `inventories`.`user_id` = {$_SESSION['user_id']}"; //query
			$invData = mysqli_query($conn, $invQuery) or DIE('Bad Query'); //execute query
			$invArr = mysqli_fetch_array($invData); //inventory as "x,y,num"
			$_SESSION['inventory'] = array(); //declare new inventory array
			foreach ($invArr as $id => $xyNum) {
				if ($id != 'user_id') { //i.e. if it corresponds to an item
					array_push($_SESSION['inventory'], explode(",", $xyNum)); //push exploded value to $_SESSION['inventory']
				} else if ($id) { //if only set to "else", an empty row is pushed ¯\_(ツ)_/¯
					array_push($_SESSION['inventory'], array("x","y",1)); //allows for validation of items with no lock (i.e. where lock_id = 0)
				}
			}
			
			//act as if they came from login
			$welcome = true;
			unset($_POST['action']); //anything typed previously should be disregarded
			
		} else {
			$welcome = false;
		}
		
		//set vars (most empty to be filled later)
		$_SESSION['referrer'] = 'index';
		$msg = "";
		$thisInv = "";
		$locObjects = "";
		$confirm = false; //only applies to leaving the island
		
		require_once('scripts/dbc.php'); //connect to db
		
		//access location information by creating and executing query, then turn it into an array
		$locQuery = "SELECT * FROM `locations` WHERE `locations`.`x` = {$_SESSION['x']} AND `locations`.`y` = {$_SESSION['y']}"; //location at player x and y
		$locData = mysqli_query($conn, $locQuery) or DIE('Bad Query');
		$locArr = mysqli_fetch_array($locData); //array of location information
		
		$oldX = $_SESSION['x']; //declare new variable at the current x. Will be checked to see if the user moved
		$oldY = $_SESSION['y']; //declare new variable at the current y. Will be checked to see if the user moved
	
		if (ISSET($_GET['leave'])) {
			if ($_GET['leave'] == "go") {
				$action = "go east";
				$confirm = true; //do that the user is not just asked to confirm again
				$_POST['action'] = "go east"; //set what the user would have inputted
			}
		}
		
		//if the user has submitted information, run text recognition to deal with input
		if (ISSET($_POST['action'])) {
			require_once("scripts/textRec.php");
		}
		
		if ($oldX != $_SESSION['x'] or $oldY != $_SESSION['y']) { //check $oldX and $oldY against current $_SESSION values to see if the location has changed.
			//if the location has changed, access location information by creating and executing query, then turn it into an array
			$locQuery = "SELECT * FROM `locations` WHERE `locations`.`x` = {$_SESSION['x']} AND `locations`.`y` = {$_SESSION['y']}";
			$locData = mysqli_query($conn, $locQuery) or DIE('Bad Query');
			$locArr = mysqli_fetch_array($locData); //array of location information
		}

		foreach ($_SESSION['inventory'] as $itemKey => $itemVals) { //go through inventory array
			if ($itemKey != 0 and $itemKey <= 13) { //if the child item array has a key from 1 to 13 (i.e. is an object that the user can interact with)
				//declare vars
				if ($itemVals[0] !== 'x') {
					$x = intval($itemVals[0]); //takes care of items with x values of 'x' (otherwise they would show on the grid at (0,0))
				} else {
					$x = $itemVals[0];
				}
				$y = $itemVals[1];
				$num = intval($itemVals[2]);
				
				if ($num != 0 or ($x === intval($_SESSION['x']) and $y == $_SESSION['y'])) { //if the user has more than 1 or the item is at this location
					//create and execute query to gain item information
					$itemQuery = "SELECT * FROM `items` WHERE `items`.`item_id` = {$itemKey}";
					$itemData = mysqli_query($conn, $itemQuery) or DIE('Bad Item Query');
					$itemArr = mysqli_fetch_array($itemData); //array of specific item information
					
					if ($num > 0) { //if the user has one or more of the item, add its informtion to the $thisInv string to notify the user.
						$thisInv .= "<img src = '{$itemArr['image']}' class = 'itemIcon' /><h3>" . $itemArr['item'] . "</h3>(" . $num . "): " . $itemArr['description'] . "<br>";
					}
					if ($x == $_SESSION['x'] and $y == $_SESSION['y'] and $num <= 0 and intval($_SESSION['inventory'][$itemArr['lock_id']][2]) !== 0) { //if the item is at this location, and the user has already picked up the item's lock (eg. to see the treasure chest, you must havethe map)
						$locObjects .= "There is " . strtolower($itemArr['item']) . " here.<br>"; //push item to the $locObjects output string
					}
				}
			}
		}
		
		if ($_SESSION['x'] == 4 and $_SESSION['y'] == 2 and !$_SESSION['inventory'][14][2]) { //If the user is at the jaguar's den and has not yet killed the jaguar, add the jaguar to the description
			$locArr['description'] .= "<br><br>A jaguar prowls the undergrowth. She looks weak; a large gash suggesting of a very recent battle oozes pus. Her coat is luscious, though, and she carries her head high, surrounded by an air of arrogance.<br><br>She looks up and snarls at you.";
		}
		if ($_SESSION['raft'] != array(0,0,0,0) and $_SESSION['x'] == 4 and $_SESSION['y'] == 4) { //if the user is at the reef and has begun building the raft
			$locObjects .= "<br>There is a raft here:";
			if ($_SESSION['raft'][0]) {
				$locObjects .= "<br> - {$_SESSION['raft'][0]} plank(s)";
			}
			if ($_SESSION['raft'][1]) {
				$locObjects .= "<br> - {$_SESSION['raft'][1]} mast(s)";
			}
			if ($_SESSION['raft'][2]) {
				$locObjects .= "<br> - {$_SESSION['raft'][2]} sail(s)";
			}
			if ($_SESSION['raft'][3]) {
				$locObjects .= "<br> - {$_SESSION['raft'][3]} jar(s) worth of resin";
			}
			$locObjects .= "<br>I wonder if it's ready to sail?";
		}
	?>
	<html>
	<head>
		<!-- Link to CSS -->
		<link rel = "stylesheet" type = "text/css" href = "scripts/isu.css" />
	</head>
	<body>
		<header>
			<!-- Display as many hearts as the number of HP the user currently has -->
			<span id = "healthBar"><?php for ($i = 0; $i < $_SESSION['health']; $i++) {
				echo "<img src = 'images/health.png' class = 'health' />";
			} ?></span>

			<!-- NAVIGATION BAR: Display message in header, as well as links to edit account, reset and logout -->
			<span class = "info">
				<span id = "usrName">
					<?php 
						if (file_exists("userImgs/{$_SESSION['user_id']}.png")) {
							echo "<img id = 'usrIcon' src = 'userImgs/{$_SESSION['user_id']}.png' /> ";
						} else {
							echo "<img id = 'usrIcon' src = 'userImgs/default.png' /> ";
						}
						echo $_SESSION['first'] . " " . $_SESSION['last']; 
					?>
				</span><br> 
				<a href = 'logout.php'>Logout</a> | 
				<a href = 'editAcc.php'>Edit Account</a> | 
				<!-- link to reset progress using $_GET superglobal -->
				<a href = 'index.php?reset=1'>Reset Progress</a>
			</span>
			
		</header>
		
		<!-- main body div -->
		<div id = "main">
			<!-- Display location name, description and objects -->
			<h1><?php echo $locArr['location'] . "</h1><br>" . $msg . $locArr['description'] . "<br><br>" . $locObjects; ?>
			
		</div>
		
		<!-- div to contain form -->
		<div id = "actionDiv">
			<!-- form -->
			<form action = "index.php" method = "POST">
				<?php if ($_SESSION['win']) { ?> <!-- If the user won, show a reset button -->
					<input type = "submit" value = "Click here to reset progress" id = "winButton" name = "win" />
				<?php } else { ?> <!-- Else show the input box -->
					<input id = "actionBox" type = "text" name = "action" placeholder = "> Do something..." autocomplete = "off" autofocus />
				<?php } ?>
			</form>
		</div>
		
		<!-- If the user's inventory is not empty, display it in a div -->
		<?php if (!empty($thisInv)) { echo "<div id = 'inventory'><h2>Inventory</h2>" . $thisInv . "</div>"; } ?>
		
		<footer>
			<!-- click on the link -->
			<span class = "info">Type <a href = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'>'help'</a> for a list of available commands. Type 'save' to save progress.</span>
		</footer>
		
		<!-- If $welcome is true, i.e. if the user just came from login, or if they typed the command 'show' -->
		<?php if ($welcome) { ?>
			<!-- modal window to show up on screen-->
			<div id="welcome-modal">
				<!-- content of modal -->
				<div id="modal-content">
					<span class="modal-close">&times;</span>
					<h1>Welcome to Rawaki!</h1>
					You have this desert island, located in the middle of the Pacific, entirely to yourself!<br>
					Of course, you don't own it; Kiribati does. But they don't know you're here, and frankly, they don't care.<br><br>
					The only issue is that you can't survive here for long.<br><br>
					Luckily, Kanton Island, which used to house a US military base, is <em>only</em> 137 km away! 
					Kanton Island has 20 or so inhabitants; the island was an important refuelling stop for smaller planes crossing the Pacific up to the 1970s.<br><br>
					Your task is to build a raft and reach Kanton Island.<br>
					Be careful; there are many perils on Rawaki Island.<br><br><br>
					Good luck!
					<span id = "need">...You'll need it</span>
				</div>
				
			</div>
			<script>
				var modal = document.getElementById('welcome-modal'); //get the modal window by id to reference it as a variable
				var close = document.getElementsByClassName("modal-close")[0]; //get the close button by class to reference it as a variable
				modal.style.display = "block"; //set display to block so that the user can see the modal. Default is display:none
				close.onclick = function() { //if user clicks the close button
					modal.style.display = "none"; //set display to none
				}
				window.onclick = function(event) { //if user clicks the window
					if (event.target == modal) { // and it is outside of the modal content div (i.e. on the welcome-modal div)
						modal.style.display = "none"; //set display to none
					}
				}
			</script>
		<?php } ?>
		<!-- play empty file as an iframe to unblock autoplay (only nedded for chrome) -->
		<iframe src = "audio/silence.mp3" allow = "autoplay" id = "audio"></iframe>
		<!-- play audio file as specified in db at location -->
		<audio autoplay loop >
			<source src = "<?php echo $locArr['audio'] ?>" type = "audio/mpeg" />
		</audio>
	</body>
	</html>
<?php } ?>
