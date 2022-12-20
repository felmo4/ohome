<?php
	session_start();
	require '../include/dbcon.php';
	$pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");
	$details = $_SESSION["Role"]=='admin' ? "Admin logged out" : "Cashier logged out";
	$pdoActivity->execute(
		array(
			'Details'       =>     $details
		)
	);
	session_destroy();
	header('location: ../index.php');
?>