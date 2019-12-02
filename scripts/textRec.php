<?php
	//vars
	$action = strtolower($_POST['action']); //convert user input to lowercase to ease validation with preg_match
	$die = false; //set the die boolean to false by default

	//checks
	if (!empty($action)) {
		
		//if the user tries to go east from the reef, ask them to confirm as they will either win or lose if they do this
		#NOTE: this is at the beginning because it WILL influence the following textdomain
		if ($_SESSION['x'] == 4 and $_SESSION['y'] == 4 and preg_match("/east/", $action) and !$confirm) { ?>
			<html>
			<script>
				function confirmButton() {
					var val; //declare empty var
					var bool = confirm("Are you sure you want to leave the island?"); //message which appears in confirm dialog
					if (bool == true) { //if user confirms, set val to delete
						val = "go";
					} else { //else set val to 0
						val = "cancel";
					}
					location.href = "index.php?leave=" + val; //reload page with $_GET['leave'] variable in url
				}
				confirmButton(); //call the function
			</script>
			</html>
		<?php exit(); //exit the script
		}
		
		//if the user uses a verb which indicates a change in location
		if (preg_match("/go/", $action) or preg_match("/move/", $action) or preg_match("/travel/", $action) or preg_match("/walk/", $action) or preg_match("/run/", $action)) {
			//if the location has a random attribute of 1 (i.e. jungle) and the user does not have the compass
			if ($locArr['random'] == 1 and !$_SESSION['inventory'][9][2]) { //$_SESSION['inventory'][9][2] refers to the compass, where the index 9 is the compass' item id and the index 2 is the number of the object
				//set a move boolean to keep the random direction loop going until the move is possible
				$move = false;
				while (!$move) {
					$randMove = rand(1,4); //pick out of four random directions
					switch ($randMove) {
						case 1:
							if ($locArr['north']) { //if the current location has a space somewhere to the north
								$_SESSION['y'] -= $locArr['north']; //take the number of spaces north away from the user's y coordinate
								$move = true; //set move to true
							}
							break; //escape the switch
						case 2:
							if ($locArr['east']) { //if the current location has a space somewhere to the east
								$_SESSION['x'] += $locArr['east']; //add the number of spaces east away to the user's x coordinate
								$move = true; //set move to true
							}
							break; //escape the switch
						case 3:
							if ($locArr['south']) { //if the current location has a space somewhere to the south
								$_SESSION['y'] += $locArr['south']; //add the number of spaces south to the user's y coordinate
								$move = true; //set move to true
							}
							break; //escape the switch
						case 4:
							if ($locArr['west']) { //if the current location has a space somewhere to the west
								$_SESSION['x'] -= $locArr['west']; //take the number of spaces west away from the user's x coordinate
								$move = true; //set move to true
							}
							break; //escape the switch
					}
				}
				$msg = "You seem to be lost in the jungle.<br>You moved, but you do not not which way you went."; //do not tell them which direction they move, only that they did move
			} else { //random = 0, 2 or -1 and therefore travel on land is not affected
				//begin common component of message
				$msg = "You went ";
				if (preg_match("/north/", $action)) {
					if ($locArr['north']) { //if it is possible to go north from the location, adjust the y coordinate and tell them they went north
						$_SESSION['y'] -= $locArr['north'];
						$msg .= "north.";
					} else if (!$_SESSION['y']) { //else if current y == 0, tell the user that there is only ocean to the north
						$msg = "The ocean lies to the north. You can't go that way.<br>...But it wouldn't hurt to jump in and try, would it?";
					} else { //tell the user they can't go in the direction they want
						$msg = "You can't go north.";
					}
				} else if (preg_match("/east/", $action)) {
					if ($locArr['east']) { //if it is possible to go east from the location, adjust the x coordinate and tell them they went east
						$_SESSION['x'] += $locArr['east'];
						$msg .= "east.";
					} else if ($_SESSION['x'] == 4) { //else if current x == 0, tell the user that there is only ocean to the east
						$msg = "The ocean lies to the east. You can't go that way.<br>...But it wouldn't hurt to jump in and try, would it?";
					} else { //tell the user they can't go in the direction they want
						$msg = "You can't go east.";
					}
				} else if (preg_match("/south/", $action)) {
					if ($locArr['south']) { //if it is possible to go south from the location, adjust the y coordinate and tell them they went south
						$_SESSION['y'] += $locArr['south'];
						$msg .= "south.";
					} else if ($_SESSION['y'] == 4) { //else if current y == 4, tell the user that there is only ocean to the south
						$msg = "The ocean lies to the south. You can't go that way.<br>...But it wouldn't hurt to jump in and try, would it?";
					} else { //tell the user they can't go in the direction they want
						$msg = "You can't go south.";
					}
				} else if (preg_match("/west/", $action)) {
					if ($locArr['west']) { //if it is possible to go west from the location, adjust the x coordinate and tell them they went west
						$_SESSION['x'] -= $locArr['west'];
						$msg .= "west.";
					} else if (!$_SESSION['x']) {
						$msg = "The ocean lies to the west. You can't go that way.<br>...But it wouldn't hurt to jump in and try, would it?";
					} else { //tell the user they can't go in the direction they want
						$msg = "You can't go west.";
					}
				} else if (preg_match("/inside/", $action)) { //if the user indicates they want to "go inside", "walk inside", etc.
					$msg = "Use 'look inside' or 'enter' instead."; //ask them to use "look" or "enter" instead
				} else { //else tell the user that the program does not recognize the direction, and give them the four acceptable directions
					$msg = "I don't understand which direction you would like to move.<br>Acceptable directions:<br>- North<br>- East<br>- South<br>- West";
				}
			}
			
		//if the user indicates they would like to save their location to the database (saving happens automatically if the user re-spawns)
		} else if (preg_match("/save/", $action)) {
			//convert raft to string
			$raft = implode(",",$_SESSION['raft']);
			
			//create and execute query to update the user's location, and raft values in the database
			$saveQuery = "UPDATE `users` SET `x` = {$_SESSION['x']}, `y` = {$_SESSION['y']}, `Raft` = '{$raft}' WHERE `users`.`user_id` = {$_SESSION['user_id']}";
			mysqli_query($conn, $saveQuery) or DIE('Bad Query');
			
			//create and execute query to update the user's inventory
			$invQuery = "UPDATE `inventories` SET "; //first bit of query
			for ($key = 1; $key <= 15; $key ++) { //go through the inventory items
				$itemVals = $_SESSION['inventory'][$key];
				$valsString = implode(",",$itemVals); //turn the item array into a string
				if ($key != 15) {
					$invQuery .= "`{$key}` = '{$valsString}', "; //push the string and the index to the query
				} else {
					$invQuery .= "`{$key}` = '{$valsString}' "; //don't add the comma on the last round
				}
			}
			$invQuery .= "WHERE `inventories`.`user_id` = {$_SESSION['user_id']}"; //add the last bit of the query
			
			mysqli_query($conn,$invQuery) or DIE('Inventory Not Saved');
			
			##NOTE 1:	Health saves automatically
			##NOTE 2:	After dying, inventory will remain the same, but location will save to re-spawn location
			
			$msg = "Progress saved."; //notify the user

		//if the user types "help"
		} else if (preg_match("/help/", $action)) {
			$msg = "Here you go:<br><br>"; //begin the message
			
			//array of all possible commands and their descriptions
			$commands = array(
				'help' => 'display a list of available commands',
				'go __' => 'travel in a specified direction (north/east/south/west)',
				'run __' => 'travel in a specified direction (north/east/south/west)',
				'walk __' => 'travel in a specified direction (north/east/south/west)',
				'move __' => 'travel in a specified direction (north/east/south/west)',
				'save' => 'save current position, inventory and game progress',
				'jump into __' => 'jump into specified body',
				'swim in __' => 'swim in specified body of water',
				'pick up __' => 'add specified item to inventory',
				'take __' => 'add specified item to inventory',
				'grab __' => 'add specified item to inventory',
				'drop __' => 'remove specified item from inventory',
				'put down __' => 'remove specified item from inventory',
				'remove __' => 'remove specified item from inventory',
				'travel __' => 'travel in a specified direction (north/east/south/west)',
				'cast line' => 'fish for fish',
				'fish' => 'fish for fish',
				'give __ to __' => 'transfer an object into another thing\'s possession',
				'feed __ to __' => 'transfer an object into another thing\'s possession for consummation',
				'attack __' => 'attack specified thing',
				'fight __' => 'attack specified thing',
				'show' => 'show objective statement',
				'add __ to __' => 'fix a specified object to another specified object',
				'attach __ to __' => 'fix a specified object to another specified object',
				'fix __ to __' => 'fix a specified object to another specified object',
				'connect __ to __' => 'fix a specified object to another specified object',
				'eat __' => 'consume specified object',
				'drink' => 'consume water',
				'open __' => 'open specified object',
				'unlock __' => 'unlock specified object',
				'look' => 'look around and inside surroundings',
				'enter' => 'go inside to look around',
				'search' => 'look around and inside surroundings',
				'read __' => 'read specified object',
				'kill __' => 'attack specified thing'
			);
			ksort($commands); //sort the array alphabetically by key (i.e. by command)
			
			//echo a table and the header row
			$msg .= "<table><tr><th>Command</th><th>Use</th></tr>";
			//go through the array an echo each command and it's functionality
			foreach ($commands as $cmd => $func) {
				$msg .= "<tr><td>{$cmd}</td><td>{$func}</td></tr>";
			}
			$msg .= "</table>";
			
		//if the user types swim or jump
		} else if (preg_match("/jump in/", $action) or preg_match("/swim/", $action)) {
			//if the user indicates they would like to swim in the ocean/sea, and they are in a location where this i possible, they die trying to swim away
			if ((preg_match("/ocean/", $action) or preg_match("/sea/", $action)) and ($_SESSION['x'] == 0 or $_SESSION['x'] == 4 or $_SESSION['y'] == 0 or $_SESSION['y'] == 4)) {
				$msg = "You jumped into the ocean and tried to swim away. Your warm body provided a delectable meal for a small tiger shark.";
				$die = true;
			//else if the user types the command at a location with a random attribute of 2
			} else if ($locArr['random'] == 2) { //these locations (the waterfall and the underground river entrance) will take the user to a random location if the user jumps in
				if ($_SESSION['inventory'][3][2]) { //if the user has a snorkel (3 is the snorkel's item id and 2 is the number of the specified item)
					$randLocID = rand(1, 18); //pick a random location id
					
					//create and execute a query to grab the x and y values from the location at the random id, and set the session x and y values to it
					$randLocQuery = "SELECT `x`, `y`, `location` FROM `locations` WHERE `locations`.`location_id` = {$randLocID}";
					$randLocArr = mysqli_fetch_array(mysqli_query($conn, $randLocQuery)) or DIE('Bad');
					$_SESSION['x'] = $randLocArr['x'];
					$_SESSION['y'] = $randLocArr['y'];
					
					//notify the user
					$msg = "You jumped in and were swept through a system of underground caves. You popped out at {$randLocArr['location']}.<br>...I'm quite impressed that you held your breath for so long!";
				
				} else { //else the user dies because they didn't have a snorkel
					$msg = "You tried to swim without a snorkel.";
					$die = true;
				}
			//else if the user types the command at a location with random attribute of 1 (jungle)
			} else if ($locArr['random'] == 1) {
				$msg = "You jumped into a bush and were bitten by a spider.";
				$die = true;
			//else if the user types the command at a river (random == -1)
			} else if ($locArr['random'] == -1) {
				if ($_SESSION['inventory'][3][2]) { //if the user has a snorkel (3 is the snorkel's item id and 2 is the number of the specified item)
					$msg = "You jumped into the river and were swept "; //begin message
					if ($locArr['x'] == 1 and $locArr['y'] == 3) { //if the user jumps in at the one point where the river splits in three
						$randMove = rand(1,3); //choose a random direction for the river to sweep them
						switch ($randMove) { //for each case move the user in the direction and break the switch
							case 1: //north
								$_SESSION['y'] -= $locArr['north'];
								$msg .= "north.";
								break;
							case 2: //east
								$_SESSION['x'] += $locArr['east'];
								$msg .= "east.";
								break;
							case 3: //west
								$_SESSION['x'] -= $locArr['west'];
								$msg .= "west.";
								break;
						}
					} else { //otherwise the river will sweep them north
						$_SESSION['y'] -= $locArr['north'];
						$msg .= "north.";
					}
					$msg .= " You scramble out of the river onto the shore."; //add that they exited the river
				
				} else { //else the user dies because they don't have a snorkel
					$msg = "You tried to swim without a snorkel.";
					$die = true;
				}
			} else { //otherwise tell the user they can't jump in here (this way, jumping in the ocean is sort of hidden)
				$msg = "There is nothing that you can jump into here.";
			}
			
		//if the user types "pick up", etc.
		} else if (preg_match("/pick up/", $action) or preg_match("/take/", $action) or preg_match("/grab/", $action)) {
			//array of key words that a user might refer to an object as, and their item ids
			$itemNames = array(
				'nut' => 1,
				//notice that fish is not listed (id = 2), because the user cannot pick the fish up; they must use a fishing rod
				'snorkel' => 3,
				'rod' => 4,
				'mast' => 5,
				'plank' => 6,
				'wood' => 6,
				'chest' => 7,
				'treasure' => 7,
				'sail' => 8,
				'canvas' => 8,
				'compass' => 9,
				'map' => 10,
				'key' => 11,
				'sword' => 12,
				'weapon' => 12,
				'jar' => 13,
				'resin' => 13
			);
			
			//go through the item name array until the item is found
			foreach ($itemNames as $name => $itemKey) {
				if (preg_match("/{$name}/", $action)) {
					//declare x, y and num vars
					$x = $_SESSION['inventory'][$itemKey][0];
					$y = $_SESSION['inventory'][$itemKey][1];
					$num = $_SESSION['inventory'][$itemKey][2];
					
					//create and execute query to get specific item information
					$itemQuery = "SELECT * FROM `items` WHERE `items`.`item_id` = {$itemKey}";
					$itemData = mysqli_query($conn, $itemQuery) or DIE('Bad Item Query');
					$itemArr = mysqli_fetch_array($itemData); //array of item information
					
					//if the item is at this location, the user does not already have the maximum number of items and the user has the lock item
					if ($x == $_SESSION['x'] and $y == $_SESSION['y'] and $num < $itemArr['max'] and intval($_SESSION['inventory'][$itemArr['lock_id']][2]) !== 0) {
						//set the number posessed to the max (keeping the x and y the same)
						$num = $itemArr['max'];
						$_SESSION['inventory'][$itemKey] = array($x,$y,$num);
						
						$msg = "You picked up " . strtolower($itemArr['item']);
						break; //break the loop

					} else { //tell the user the item isn't here (even if it is, but they just can't see it cause it's locked)
						$msg = "The object you're trying to take isn't at this location.";
						break;
					}
				//else if the user tries to pick up the fish
				} else if (preg_match("/fish/", $action)) {
					if ($_SESSION['inventory'][2][0] == $_SESSION['x'] and $_SESSION['inventory'][2][1] == $_SESSION['y']) { //if the fish is at this location
						$msg = "You tried to grab the fish with your hands, but it was too slippery."; //tell them they can't
					} else { //there are no fish here
						$msg = "There are no fish here, silly.";
					}

				} else if (preg_match("/self/", $action)) {
					$msg = "No, silly. You cant' pick yourself up!";
					break; //break the loop
				} else { //tell them that the program doesn't recognize what they want to pick up
					$msg = "I don't understand which object you would like to pick up.";
				}
			}
		
		//if the user types "drop", etc.
		} else if (preg_match("/drop/", $action) or preg_match("/remove/", $action) or preg_match("/put down/", $action)) {
			//array of key words that a user might refer to an object as, and their item ids
			$itemNames = array(
				'nut' => 1,
				'fish' => 2,
				'snorkel' => 3,
				'rod' => 4,
				'mast' => 5,
				'plank' => 6,
				'wood' => 6,
				'chest' => 7,
				'treasure' => 7,
				'sail' => 8,
				'canvas' => 8,
				'compass' => 9,
				'map' => 10,
				'key' => 11,
				'sword' => 12,
				'weapon' => 12,
				'jar' => 13,
				'resin' => 13
			);
			
			//go through the item name array until the item is found
			foreach ($itemNames as $name => $itemKey) {
				if (preg_match("/{$name}/", $action)) {
					//declare x, y and num vars
					$x = $_SESSION['inventory'][$itemKey][0];
					$y = $_SESSION['inventory'][$itemKey][1];
					$num = $_SESSION['inventory'][$itemKey][2];
					
					//create and execute query to get specfific item information
					$itemQuery = "SELECT * FROM `items` WHERE `items`.`item_id` = {$itemKey}";
					$itemData = mysqli_query($conn, $itemQuery) or DIE('Bad Item Query');
					$itemArr = mysqli_fetch_array($itemData); //array of item information
					
					//if the user has more than one of the item
					if ($num > 0) {
						//set item x and y to location and num to -1
						$_SESSION['inventory'][$itemKey] = array($_SESSION['x'],$_SESSION['y'],-1);
						
						$msg = "You dropped " . strtolower($itemArr['item']); //notify the user
						break; //break the loop
					
					} else { //else the object isn't here
						$msg = "The object you're trying to drop isn't in your inventory.";
						break;
					}
				} else if (preg_match("/self/", $action)) {
					$msg = "You attempted to pick yourself up in order to drop yourself.<br>You failed.";
					break; //break the loop
				} else { //else tell user that the program does not understand the object given
					$msg = "I don't understand which object you would like to drop.";
				}
			}
			
			//else if the user indicates they would like to drop everything
			if (preg_match("/everything/", $action)) {
				//go through each id, creating and executing a query that sets the object to the current location with a num of -1 (only if the user has the item)
				for ($i = 1; $i <= 13; $i++) {
					if ($_SESSION['inventory'][$i][2] > 0) {
						$_SESSION['inventory'][$i] = array($_SESSION['x'],$_SESSION['y'],-1);
					}
				}
				$msg = "You completely emptied your inventory.";
			}
			
		//if the user indicates they would like to feed something
		} else if (preg_match("/feed/", $action) or preg_match("/give/", $action)) {
			//if the user references the snake
			if (preg_match("/snake/", $action)) {
				if ($_SESSION['x'] == 3 and $_SESSION['y'] == 3) { //check that they are at the right location
					if (preg_match("/fish/", $action)) { //if they want to give the fish to the snake
						if ($_SESSION['inventory'][2][2]) { //if the user has the snake
							$_SESSION['inventory'][2] = array('x','x',0); //remove the fish from the inventory and take it off the map
							$_SESSION['inventory'][15] = array('x','x',1); //set the "snake" item in the inventory to one, to signify the snake has been beaten (i.e. to unlock the sword)
							
							$msg = "The snake gladly accepts this new treat. In exchange, it reveals a sword which it has been guarding in its nest.<br>It seems as if the snake would like to give you the sword."; //notify th user
						
				//notify the user why they could not complete the action for various ifs
						} else {
							$msg = "You do not have a fish to give to the snake.";
						}
					} else {
						$msg = "The snake does not accept your offer.";
					}
				} else {
					$msg = "There is no snake here.";
				}
				
			//else if the user references the jaguar
			} else if (preg_match("/jaguar/", $action)) {
				if ($_SESSION['x'] == 4 and $_SESSION['y'] == 2 and !$_SESSION['inventory'][14][2]) { //if they are at the jaguar's den and they have beaten the jaguar
					$msg = "The jaguar does not accept your offer."; //don't let them give anything to the jaguar
				} else {
					$msg = "There is no jaguar here."; //else tell them there is no jaguar
				}
			} else { //else they are trying to give something to an object. Don't let them
				$msg = "It seems as if you're trying to give something to an inanimate object...";
			}
			
		//if the user types "fight" or "attack" or "kill"
		} else if (preg_match("/fight/", $action) or preg_match("/attack/", $action) or preg_match("/kill/", $action)) {
			//if they reference the jaguar
			if (preg_match("/jaguar/", $action)) {
				if ($_SESSION['x'] == 4 and $_SESSION['y'] == 2 and !$_SESSION['inventory'][14][2]) { //if they are at the jaguar den and they have not yet killed the jaguar
					if ($_SESSION['inventory'][12][2]) { //if the user has the sword in their inventory
						
						//jaguar fight sequence. A random number is picked (between 0 an 20), which determines the outcome (win, loss or draw). The user is notified after each turn if they won, lost, or tied
						$randOutcome = rand(0,20); //random number
						if ($randOutcome <= 11 ) { // 57% chance -> draw
							$msg = "You lunged at the jaguar with your sword, but it leaped out of the way. It isn't quite as injured as it seems.<br>It snarls, awaiting the next attack.";
						} else if ($randOutcome <= 19) { // 38% chance -> win
							$_SESSION['inventory'][14] = array('x','x',1); //set the jaguar inventory value to defeated
							$msg = "You slash the jaguar with your sword.<br>It parries with a slash at your ankle, drawing much blood.<br>As it retreats, you stab out and manage to connect. The jaguar bows down, accepting defeat, before limping away into the bushes. <br><br>A glint of metal in the sand catches your eye.";
						} else { // 5% chance -> loss
							$msg = "You thrust at the jaguar with your sword, but trip on  large root at your feet, falling on your stomach.<br>The jaguar is happy for such a tasty meal.";
							$die = true; //kill the user
						}
					} else { //otherwise, if the user has no weapon
						$msg = "For lack of a better weapon, you attacked the jaguar with your hands.<br>Even though she was wounded, the jaguar made easy work of you.";
						$die = true; //kill them
					}
				} else { //else there is no jaguar to fight
					$msg = "There is no jaguar here.";
				}
			// if the user references the snake
			} else if (preg_match("/snake/", $action)) {
				if ($_SESSION['x'] == 3 and $_SESSION['y'] == 3) { //if they are at the snake nest
					$msg = "You attacked the snake.<br>It was a mighty fight, but just when it seemed you would win, the snake snuck in and bit you in the calf.<br>You hobbled away in agony.";
					$die = true; //kill them no matter what
				} else { //else there is no snake
					$msg = "There is no snake here.";
				}
			} else if (preg_match("/self/", $action)) { //else if they try to attack themselves
				$msg = "You beat yourself up and lost a life.";
				$die = true; //kill them
			} else { //else they are trying to attack an object
				$msg = "You tried to attack an inanimate object, but the object got the best of you.";
			}
			
		//if they type "show"
		} else if (preg_match("/show/", $action)) {
			$welcome = true; //set the welcome boolean to true to show the modal window
			$msg = "Read carefully";

		//if the user types "eat"
		} else if (preg_match("/eat/", $action)) {
			if (preg_match("/nut/", $action)) { //if they want to eat the coconut
				if ($_SESSION['inventory'][1][2]) { //if they have a coconut
					
					$_SESSION['inventory'][1][2]--; //take away a coconut from the number they have
					$_SESSION['inventory'][1][0] = 'x'; //set x to 'x' to remove object from map
					$_SESSION['inventory'][1][1] = 'x'; //set y to 'x' for continuity

					$msg = "You opened the coconut on a rock. You ate the coconut"; //tell them they ate a coconut
					
					//if they have not yet  gained 3 HP, give them one extra HP and increase the 'HPGained' variable by one, rnu and execute query
					if ($_SESSION['HPGained'] < 3) {
						$_SESSION['health']++;
						$_SESSION['HPGained']++;
						$eatQuery = "UPDATE `users` SET `health` = {$_SESSION['health']}, `HPGained` = {$_SESSION['HPGained']} WHERE `users`.`user_id` = {$_SESSION['user_id']}"; //query
						mysqli_query($conn,$eatQuery) or DIE('Barfed without ingesting');
						$msg .= " and gained 1 HP"; //tell them they gained an hp
					}
					$msg .= "."; //add a period at the end
				} else if ($_SESSION['inventory'][1][0] == $_SESSION['x'] and $_SESSION['inventory'][1][1] == $_SESSION['y']) { //else if the coconut is at the location, but the user hasn't picked it up, tell then to pick it up first
					$msg = "You need to pick up the coconut in order to eat it.";
				} else { //else there are no coconuts
					$msg = "There are no coconuts here.";
				}
			//else if they try to eat the fish
			} else if (preg_match("/fish/", $action)) {
				if ($_SESSION['inventory'][2][2]) { //if they have a fish
					$msg = "You took a small bite out of the fish. It tasted gross.<br>A few hours later, you became very sick.<br>I wouldn't eat unknown species if I were you. ";
					$die = true; //fish makes them sick and they die
				} else if ($_SESSION['inventory'][2][0] == $_SESSION['x'] and $_SESSION['inventory'][2][1] == $_SESSION['y']) { //else if the fish is here but they don't have it, tell them to catch it
					$msg = "You need to catch the fish in order to eat it.";
				} else { //else there are no fish
					$msg = "There are no fish here.";
				}
			} else { //else, they can't eat the thing they want to eat
				$msg = "You can't eat that.";
			}
			
		//if the user types "drink"
		} else if (preg_match("/drink/", $action)) {
			if (($_SESSION['x'] == 0 and $_SESSION['y'] == 3) or ($_SESSION['x'] == 1 and $_SESSION['y'] == 4)) { //if they are at a location with FRESH water (waterfall or spring )
				$msg = "The water here is fresh.<br>You take big gulps of water, and for the first time in days you aren't thirsty anymore.";
				if ($_SESSION['HPGained'] < 3) { //if they haven't yet gained 3 hp, let them gain one hp
					$_SESSION['health']++; //increase hp
					$_SESSION['HPGained']++; //increase hpgained
					//create and execute query to update health
					$drinkQuery = "UPDATE `users` SET `health` = {$_SESSION['health']}, `HPGained` = {$_SESSION['HPGained']} WHERE `users`.`user_id` = {$_SESSION['user_id']}";
					mysqli_query($conn,$drinkQuery) or DIE('Water Regurgitated');
					$msg .= "<br>You gained 1 HP."; //add the HP gain to the message
				}
			} else if ($locArr['random'] == -1 or $locArr['random'] == 2) { //else if they are at a river (random = -1) (random = 2 is the underground river entrance)
				$msg = "You drink a few mouthfuls of the water. It's muddy and gross.<br>After a few hours you catch a high fever.";
				$die = true; //the water makes them sick and they lose a life
			} else if ($_SESSION['x'] == 0 or $_SESSION['y'] == 0 or $_SESSION['x'] == 4 or $_SESSION['y'] == 4) { //else if they are at an ocean, tell them not to drink salt water
				$msg = "The only water here is salt water.<br>You can't drink salt water.";
			} else { //else there is no water there
				$msg = "There is no water here.";
			}
			
		//if the user wants to "open" or "unlock" something
		} else if (preg_match("/open/", $action) or preg_match("/unlock/", $action)) {
			#NOTE: the only thing that can actually be unlocked is the chest
			if (preg_match("/chest/", $action)) { //if they reference the chest
				if ($_SESSION['inventory'][7][2] or ($_SESSION['x'] == $_SESSION['inventory'][7][0] and $_SESSION['y'] == $_SESSION['inventory'][7][1])) { //if the chest is there or if the chest is in their inventory
					if ($_SESSION['inventory'][11][2]) { //if they have the key
						$msg = "You used the dirty key to open the treasure chest.<br>Upon opening the chest, a large canvas sail falls out onto the ground.<br>You bask in the glimmer of the gold and diamonds which fill the chest to the brim."; //tell them the chest opened
						$_SESSION['inventory'][8] = array($_SESSION['x'],$_SESSION['y'],0); //set the sail to the user's location
					} else { //else they don't have the key; tell them it's locked
						$msg = "You attempted to open the treasure chest, but discovered that it was locked.<br>I wonder where the key is?";
					}
				} else { //else there is no chest
					$msg = "There is no treasure chest here.";
				}
			//else if they want to open the jar
			} else if ((preg_match("/jar/", $action) or preg_match("/resin/", $action)) and ($_SESSION['inventory'][13][2] or ($_SESSION['x'] == $_SESSION['inventory'][13][0] and $_SESSION['y'] == $_SESSION['inventory'][13][1]))) { // and if they have the jar or the jar is at the location
				$msg = "It is already open."; //tell them it is open
			} else { //else there is no jar
				$msg = "There is nothing to open here.";
			}
			
		//else if the user wants to "look" or "enter" or "search"
		} else if (preg_match("/look/", $action) or preg_match("/enter/", $action) or preg_match("/search/", $action)) {
			//if they are at the barren rocks
			if ($_SESSION['x'] == 4 and $_SESSION['y'] == 0) {
				$msg = "You looked inside the cracks of the rocks.<br>";
				if ($_SESSION['inventory'][9][0] == 'x') { //if they have not yet found the compass
					$_SESSION['inventory'][9] = array(4,0,0); //set the compass to the location
					$msg .= "You noticed something shiny.";
				} else { //else they didn't see anything
					$msg .= "You didn't see anything.";
				}
			//if they are at the cave
			} else if ($_SESSION['x'] == 4 and ($_SESSION['y'] == 1)) {
				$msg = "You looked inside the cave.<br>";
				if ($_SESSION['inventory'][10][0] == 'x') { //if they have not yet found the map
					$_SESSION['inventory'][10] = array(4,1,0); //set the map to the location
					$msg .= "You noticed something on the floor.";
				} else { //else they didn't see anything
					$msg .= "You didn't see anything.";
				}
			//if they are at the shipwreck
			} else if ($_SESSION['x'] == 0 and ($_SESSION['y'] == 0)) {
				$msg = "You looked inside the shipwreck.<br>";
				if ($_SESSION['inventory'][5][0] == 'x') { //if they have not yet found the mast
					$_SESSION['inventory'][5] = array(0,0,0); //set map to the location
					$msg .= "You noticed a piece of the ship that seemed as if it had not rotten.";
				} else { //else they didn't see anything
					$msg .= "You didn't see anything.";
				}
			//if they are at the fishing hut
			} else if ($_SESSION['x'] == 1 and ($_SESSION['y'] == 1)) {
				$msg = "You looked inside the hut.<br>";
				if ($_SESSION['inventory'][3][0] == 'x') { //if they have not yet found the resin and snorkel
					$_SESSION['inventory'][3] = array(1,1,0); //set snorkel to location
					$_SESSION['inventory'][13] = array(1,1,0); //set resin to location
					$msg .= "You noticed two objects sitting on a rotten table.";
				} else { //else they didn't see anything
					$msg .= "You didn't see anything.";
				}
			} else { //else tell them it looks nice
				$msg = "You looked around. It is very pretty here.";
			}
		
		//if the user wants to read
		} else if (preg_match("/read/", $action)) {
			if (preg_match("/map/", $action)) { //if they reference the map
				if ($_SESSION['inventory'][10][2]) {//if they have the map, tell them where the treasure is
					$msg = "You read the map.<br>You notice a red <span class = 'red'>x</span> two spaces south and one space west of the cave in which you found the map.";
				} else if ($_SESSION['inventory'][10][0] == $_SESSION['x'] and $_SESSION['inventory'][10][1] == $_SESSION['y']) { //else if the map is at the location, tell them to pick up the map
					$msg = "You need to pick up the map in order to read it.";
				} else { //else there is no map
					$msg = "There is no map here.";
				}
			} else {
				$msg = "You can't read that."; //otherwise there is nothing to read
			}
		
		//else if they want to "use" an object
		} else if (preg_match("/use/", $action)) {
			//array of object names with accompanying messages and item IDs
			$objUses = array(
				'nut' => array("You can't 'use' a nut.<br>...but you could eat it.", 1),
				'fish' => array("You can't 'use' a fish.<br>...but you could feed it to something.", 2),
				'snorkel' => array("As long as you have the snorkel in your inventory, you can swim.", 3),
				'rod' => array("To use the fishing rod, type 'cast rod' or 'fish'", 4),
				'mast' => array("I think you should attach this to your raft. That's the only use I can see for it.", 5),
				'plank' => array("I think you should attach this to your raft. That's the only use I can see for it.", 6),
				'wood' => array("I think you should attach this to your raft. That's the only use I can see for it.", 6),
				'chest' => array("The chest sits there and does nothing.", 7),
				'treasure' => array("The chest sits there and does nothing.", 7),
				'sail' => array("I think you should attach this to your raft. That's the only use I can see for it.", 8),
				'canvas' => array("I think you should attach this to your raft. That's the only use I can see for it.", 8),
				'compass' => array("As long as you have the compass in your inventory, you won't get lost in the jungle.", 9),
				'map' => array("Type 'read map' instead.", 10),
				'key' => array("Type 'unlock chest' instead.", 11),
				'sword' => array("Type 'attack', 'fight' or 'kill' instead.", 12),
				'weapon' => array("Type 'attack', 'fight' or 'kill' instead.", 12),
				'jar' => array("I think you should put the resin on your raft. That's the only use I can see for it.", 13),
				'resin' => array("I think you should put this to on raft. That's the only use I can see for it.", 13)
			);
			//go through array and search for item
			foreach ($objUses as $item => $childArr) {
				if (preg_match("/{$item}/", $action)) {
					if ($_SESSION['inventory'][$childArr[1]][2]) { //if they have the item in their inventory
						$msg = $childArr[0]; //echo the message stored in the child array
					} else { //else the object  isn't in user's inventory
						$msg = "The object you are trying to use isn't in your inventory.";
					}
					break; //break the loop
				} else { //else the object doesn't exist
					$msg = "It seems as if the at object doesn't exist. Try checking your spelling.";
				}
			}
		
		//else if they want to "fish", "cast line", etc.
		#NOTE: this is at the end of the script to avoid preg_matching when the user wants to do sometjing with the fish (eg. drop fish)
		} else if (preg_match("/fish/", $action) or preg_match("/cast/", $action) or preg_match("/rod/", $action) or preg_match("/line/", $action)) {
			if ($_SESSION['inventory'][4][2]) { //if they have the fishing rod
				if ($_SESSION['x'] == $_SESSION['inventory'][2][0] and $_SESSION['y'] == $_SESSION['inventory'][2][1] and !$_SESSION['inventory'][2][2]) { //if the fish is at the location but NOT in the user's inventory
					$_SESSION['inventory'][2] = array($_SESSION['x'],$_SESSION['y'],1); //put the fish in the user's inventory
					$msg = "You cast your line and immediately caught the fish.";
				} else if ($locArr['random'] == -1 or $_SESSION['x'] == 0 or $_SESSION['y'] == 0 or $_SESSION['x'] == 4 or $_SESSION['y'] == 4) { //else if they are at a river or the ocean, tell them they cast their line but didn't get a fish
					$msg = "You cast your rod and waited.<br>...and waited<br>...and waited<br>...<br>You reeled in the line.";
				} else { //else they are on land
					$msg = "You must be crazy! You can't fish on land!";
				}
			} else { //else they don't have a fishing rod
				$msg = "You can't fish without a fishing rod!";
			}
			
		//if the user types "add", etc.
		} else if (preg_match("/add/", $action) or preg_match("/fix/", $action) or preg_match("/attach/", $action) or preg_match("/connect/", $action)) {
			if ($_SESSION['x'] == 4 and $_SESSION['y'] == 4) { //check that the user is at the launch site
				
				//declare vars that are equal to the $_SESSION['raft'] indexes
				$plank = 0;
				$mast = 1;
				$sail = 2;
				$resin = 3;
				
				if (preg_match("/plank/", $action) or preg_match("/wood/", $action)) { //if they reference the planks
					if ($_SESSION['inventory'][6][2]) { //and they have some
						$objOne = 'plank';
						$keyOne = 6; //key of the plank in $_SESSION['inventory']
					} else { //else they don't have one
						$msg = "You do not have any planks in your inventory.";
					}
				} else if (preg_match("/mast/", $action)) { //if they reference the mast
					if ($_SESSION['inventory'][5][2]) { //and they have one
						$objOne = 'mast';
						$keyOne = 5; //key of the mast in $_SESSION['inventory']
					} else { //else they don't have one
						$msg = "You do not have a mast in your inventory";
					}
				} else if (preg_match("/sail/", $action) or preg_match("/canvas/", $action)) { //if they reference the sail
					if ($_SESSION['inventory'][8][2]) { //and they have one
						$objOne = 'sail';
						$keyOne = 8; //key of the sail in $_SESSION['inventory']
					} else { //else they don't have one
						$msg = "You do not a sail  in your inventory.";
					}
				} else if (preg_match("/resin/", $action)) { //if they reference the resin
					if ($_SESSION['inventory'][13][2]) { //and they have some
						$objOne = 'resin';
						$keyOne = 13; //key of the resin in $_SESSION['inventory']
					} else { //else they don't have one
						$msg = "You do not have any resin in your inventory.";
					}
				} else { //else the object has no use or doesn't exist
					$msg = "Attaching this object did nothing, so you removed it.";
				}
				
				//if there is a first object
				if (ISSET($objOne)) {
					if ((preg_match("/plank/", $action) or preg_match("/wood/", $action)) and $objOne != 'plank') { //if they reference the planks and it isn't the first object
						if ($_SESSION['inventory'][6][2]) { //and they have some
							$objTwo = 'plank';
							$keyTwo = 6; //key of the plank in $_SESSION['inventory']
						} else if ($_SESSION['raft'][0]) { //or if it is in the raft
							$objTwo = 'raft';
						} else { //else they don't have one
							$msg = "You do not have any planks in your inventory";
						}
					} else if (preg_match("/mast/", $action) and $objOne != 'mast') { //if they reference the mast and it isn't the first object
						if ($_SESSION['inventory'][5][2] > 0 or $_SESSION['raft'][0]) { //and they have one
							$objTwo = 'mast';
							$keyTwo = 5; //key of the mast in $_SESSION['inventory']
						} else if ($_SESSION['raft'][1]) { //or if it is in the raft
							$objTwo = 'raft';
						} else { //else they don't have one
							$msg = "You do not have a mast in your inventory";
						}
					} else if ((preg_match("/sail/", $action) or preg_match("/canvas/", $action)) and $objOne != 'sail') { //if they reference the sail and it isn't the first object
						if ($_SESSION['inventory'][8][2]) { //and they have one
							$objTwo = 'sail';
							$keyTwo = 8; //key of the sail in $_SESSION['inventory']
						} else if ($_SESSION['raft'][2]) { //or if it is in the raft
							$objTwo = 'raft';
						} else { //else they don't have one
							$msg = "You do not a sail  in your inventory";
						}
					} else if (preg_match("/resin/", $action) and $objOne != 'resin') { //if they reference the resin and it isn't the first object
						if ($_SESSION['inventory'][13][2]) { //and they have some
							$objTwo = 'resin';
							$keyTwo = 13; //key of the resin in $_SESSION['inventory']
						} else if ($_SESSION['raft'][3]) { //or if it is in the raft
							$objTwo = 'raft';
						} else { //else they don't have one
							$msg = "You do not have any resin in your inventory";
						}
					} else if (preg_match("/raft/", $action) or preg_match("/boat/", $action)) { //if they reference the raft as the last object instead
						if ($_SESSION['raft'] !== array(0,0,0,0)) { //if the raft is not empty
							$objTwo = 'raft';
						} else { //else they don't have one
							$msg = "There is no raft yet.";
						}
					} else { //attaching the second object is useless
						$msg = "Attaching one of the objects did nothing, so you removed it.";
					}
				}
				
				//if two things are set to be combined, proceed
				if (ISSET($objOne) and ISSET($objTwo)) {
					if ($objTwo != 'raft') { //if there are two objects not including the raft
						//increase the raft array at the index specified in the variables above and decrease the object in the user's inventory
						$_SESSION['raft'][$$objOne] ++;
						$_SESSION['raft'][$$objTwo] ++;
						$_SESSION['inventory'][$keyOne][2] --;
						$_SESSION['inventory'][$keyTwo][2] --;
						
						//set the objects' x and y values to x to remove them from the map
						$_SESSION['inventory'][$keyOne][0] = 'x'; //x
						$_SESSION['inventory'][$keyOne][1] = 'x'; //y
						$_SESSION['inventory'][$keyTwo][0] = 'x'; //x
						$_SESSION['inventory'][$keyTwo][1] = 'x'; //y
						
						$msg = "You added a {$objOne} to a {$objTwo}."; //tell them the objects they added together
					} else { //else if the raft was one of the objects, only one object has to undergo the processes from above
						$_SESSION['raft'][$$objOne] ++; //increase raft value
						$_SESSION['inventory'][$keyOne][2] --; //decrease inventory value
						
						$_SESSION['inventory'][$keyOne][0] = 'x'; //hide x
						$_SESSION['inventory'][$keyOne][1] = 'x'; //hide y
						
						$msg = "You added a {$objOne} to the raft."; //tell them what thay added
					}
				} else { //else tell them to specify the two things
					$msg .= "<br>--> You must specify two valid objects to connect.";
				}
				
			//else tell the user to go to the launch site first
			} else {
				$msg = "Don't build your raft here; you can't launch it from here.<br>It would be really hard to drag the entire raft around.";
			}
		
		//else if the user types "build" or "create"
		} else if (preg_match("/build/", $action) or preg_match("/create/", $action)) {
			$msg = "Your command is ambiguous. Try 'add' or 'attach' to fix two objects together."; //tell them to use "add", "attach", "fix" command instead
		
		//else if the user wants to launch the raft
		} else if (preg_match("/launch/", $action) or preg_match("/set sail/", $action) or preg_match("/leave/", $action)) {
			//if they have made a raft
			if ($_SESSION['raft'] != array(0,0,0,0)) {
				if ($_SESSION['x'] == 4 and $_SESSION['y'] == 4) { //if they are at the reef
					$msg = "To launch your raft, type 'go east', 'move east', etc."; //tell them to move east
				} else { //else tell them to go to the reef first
					$msg = "You need to be at the reef with your raft to launch it.";
				}
			
			//else tell them there isn't a raft
			} else {
				$msg = "You can't launch a raft that doesn't exist.";
			}
		
		//else if they type a direction with no verb
		#NOTE: this is at end to avoid matching with other things by accident
		} else if (preg_match("/north/", $action) or preg_match("/east/", $action) or preg_match("/south/", $action) or preg_match("/west/", $action)) {
			$msg = "You need to add a verb to that direction. Try 'go' or 'move', for example.";
		
		//else tell the user that their command is not recognized
		} else {
			$msg = "I don't understand what you mean by '{$action}'.<br><br>Type 'help' for a list of available commands.";
		}
	
	//else tell the user their command is empty
	} else {
		$msg = "Oops, it seems that you haven't typed anything.";
	}
	
	//if the user tried to move across water without a snorkel (shipwreck: x==0 y==0 or island: x==0 y==4)
	if ((($_SESSION['x'] == 0 and $_SESSION['y'] == 0) or ($_SESSION['x'] == 0 and $_SESSION['y'] == 4)) and $_SESSION['inventory'][3][2] < 1) { //$_SESSION['inventory'][3][2] refers to the snorkel, where the index 3 is the snorkel's item id and the index 2 is the number of the item specified
		//Create message to user to tell them why they died and set die = true
		$msg = "You tried to wade into the shallow water without a snorkel.<br>Suddenly, you stepped off a drop-off in the reef and found yourself with water above your head.";
		$die = true;
	}
	
	//if the user sets out onto the ocean from the reef
	if ($_SESSION['x'] == 5 and $_SESSION['y'] == 4) {
		if ($_SESSION['raft'] == array(0,0,0,0)) { //if there is no raft
			$die = true; //kill them
			$msg = "You swam into the ocean, but didn't make it far.";
		} else if ($_SESSION['raft'] != array(3,1,1,2)) { //else if they did build a raft but it wasn't correct
			$drown = false; //do not kill the user if they somehow accidentally added MORE to the raft than needed (i.e. they added some planks, dropped the remaining plank, and picked them up again, gaining MORE planks)
			
			//if the raft is not the correct inventories, check each required object to see if the user hasn't added enough. If this is so, set drown to true and add a notification to $msg
			if ($_SESSION['raft'][0] < 3) {
				$msg .= "<br>You did not add enough planks to your raft...";
				$drown = true;
			}
			if ($_SESSION['raft'][1] < 1) {
				$msg .= "<br>You did not add a mast to your raft...";
				$drown = true;
			}
			if ($_SESSION['raft'][2] < 1) {
				$msg .= "<br>You did not add a sail to your raft...";
				$drown = true;
			}
			if ($_SESSION['raft'][3] < 2) {
				$msg .= "<br>You did not add enough waterproofing resin to your raft...";
				$drown = true;
			}
			
			//if drown was true
			if ($drown) {
				$die = true;
				$_SESSION['health'] = 0; //fully kill the user (game over)
				$msg .= "Your raft sunk.<br><br><b>You couldn't make it back to shore.</b>";
			} else { //else they win because their raft was actually good
				$_SESSION['win'] = true;
			}
		} else if ($_SESSION['inventory'][7][2] > 0) { // else if they brought the treasure chest with them
			$die = true;
			$_SESSION['health'] = 0; //sink 'em
			$msg = "The treasure chest was too heavy...<br>It sunk your raft.<br><br><b>You couldn't make it back to shore.</b>";
		} else { //otherwise their raft is good and they win
			$_SESSION['win'] = true;
		}
		if ($_SESSION['win']) { //if they won, set the win value to true in the db and save their location
			//convert raft to string
			$raft = implode(",",$_SESSION['raft']);
			
			//save inventory, x, y, raft and set win to 1
			$winQuery = "UPDATE `users` SET `x` = 5, `y` = 4, `raft` = '{$raft}', `win` = 1 WHERE `users`.`user_id` = {$_SESSION['user_id']}";
			mysqli_query($conn, $winQuery) or DIE('Bad Win Query');
			#inventory#
			$invQuery = "UPDATE `inventories` SET "; //first bit of query
			for ($key = 1; $key <= 15; $key ++) { //go through the inventory items
				$itemVals = $_SESSION['inventory'][$key];
				$valsString = implode(",",$itemVals); //turn the item array into a string
				if ($key != 15) {
					$invQuery .= "`{$key}` = '{$valsString}', "; //push the string and the index to the query
				} else {
					$invQuery .= "`{$key}` = '{$valsString}' "; //don't add the comma on the last round
				}
			}
			$invQuery .= "WHERE `inventories`.`user_id` = {$_SESSION['user_id']}"; //add the last bit of the query
			
			mysqli_query($conn,$invQuery) or DIE('Inventory Not Saved'); //run inventory query
		}
	}
	
	//if the user did something that caused them to die
	if ($die) {
		$_SESSION['health'] --; //subtract one health point from the user's current number of health points
		
		//if they still have at least one life left:
		if ($_SESSION['health'] > 0) {
			//pick a random location id at which to re-spawn the user
			$respawnIDs = array(2,3,4,5,6,7,8,9,10,11,12,13,14,16,17); //only re-spawn at locations where user will not potentially die upon arrival (i.e. not in water)
			$randKey = rand(0,14);
			$randLocID = $respawnIDs[$randKey];
			
			//create and execute query to grab x and y coordinates from the location with the randomly selected id
			$randQuery = "SELECT `x`, `y` FROM `locations` WHERE `locations`.`location_id` = {$randLocID}";
			$randLoc = mysqli_query($conn, $randQuery) or DIE('Bad Re-spawn Location');
			$randPosArr = mysqli_fetch_array($randLoc); //array with the x value and y value of the location
			
			//create and execute query to update user's health an location in the database
			$respawnQuery = "UPDATE `users` SET `x` = {$randPosArr['x']},`y` = {$randPosArr['y']},`health` = {$_SESSION['health']} WHERE `users`.`user_id` = {$_SESSION['user_id']}";
			mysqli_query($conn, $respawnQuery) or DIE('Bad Health Query');
			
			//set the session x and y values and append a string to the message to tell the user they died
			$_SESSION['x'] = $randPosArr['x'];
			$_SESSION['y'] = $randPosArr['y'];
			$msg .= "<br><br>You died and lost 1 HP.<br>You re-spawned at a random location.";
			
		} else { //user's HP = 0
			//set location to "game over" location
			$_SESSION['x'] = 5;
			$_SESSION['y'] = 5;
			
			//tell them they're dead
			$msg .= "<br><br>You died and lost your last life.";
			
			//tell index upon reload that the user just died
			$_SESSION['referrer'] = 'dead';
		}
	}
	
	//Add a green "> " to the beginning of the message and two line breaks at the end
	$msg = "<span class = 'leader'>></span> " . $msg . "<br><br>";
?>