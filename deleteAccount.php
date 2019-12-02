<?php
	session_start();
	if (!empty($_POST['email'])) {
		$email = $_POST['email'];
		$pwd = $_POST['pass'];
		require_once('dbc.php');
		$query = "SELECT * FROM `users` WHERE `users`.`ACTIVE` = 1";
		$userData = mysqli_query($conn, $query) or DIE('Bad Query<br>' . mysqli_error($conn));
		while ($row = mysqli_fetch_array($userData)) {
			if ($email == $row['email']) {
				$verifyBool = password_verify($pwd, $row['password']);
				if ($verifyBool) {
					$delete = "UPDATE `users` SET `ACTIVE` = 0 WHERE `users`.`email` = '{$email}'";
					mysqli_query($conn, $delete) or DIE('Bad Query');
					session_destroy();
					header("location:deleted.html");
					die();
				} else {
					$pwdMsg = true;
				}
				break;
			}
		}
		if (ISSET($pwdMsg)) {
			$msg = "Password does not match.";
		} else {
			$msg = "No account found with {$email}";
		}
	}
	if (ISSET($_SESSION['user_id'])) {
		$backTo = "game";
	} else {
		$backTo = "login";
	}
?>
<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "isu.css" />
</head>
<body>
	<header>
		<h1>Delete Your Account</h1>
		or <a href = "login.php">back to <?php echo $backTo; ?></a>
	</header>
	<?php if (!empty($msg)) { echo "<div>" . $msg . "</div>"; } ?>
	<div><form method = "POST" action = "deleteAccount.php">
		<input type = "text" name = "email" placeholder = "Email" />
		<input type = "password" name = "pass" placeholder = "Password" />
		<input type = "submit" name = "sub" value = "Delete" />
	</form></div>
</body>
</html>