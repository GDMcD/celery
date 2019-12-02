<?php
	session_start();
	if (ISSET($_SESSION['user_id'])) {
		session_destroy(); //kill the user's session if there is an existing session
		session_start(); //begin new session to keep referrer data
		$_SESSION['referrer'] = 'logout'; //tell login page that user just logged out
	} 
	header("location:login.php"); //send user to login no matter what
	die();
?>