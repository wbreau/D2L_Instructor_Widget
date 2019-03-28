<?php
include("database/database.php");
session_start();
if(isset($_SESSION['context_id'])) {
    $courseid = $_SESSION['context_id'];
    }
	
$db = database_connect();
	$inst_name = $db->escape_string($_POST['inst_name']);
	$email = $db->escape_string($_POST['email']);
	$phone = $db->escape_string($_POST['phone']);
	$bio = $db->escape_string($_POST['bio']);
	$office = $db->escape_string($_POST['office']);
	$office_hours = $db->escape_string($_POST['office_hours']);
	
if($db != null) {
	$query = "UPDATE instructor_info SET inst_name='".$inst_name."', email='".$email."', phone='".$phone."', bio='".$bio."', office='".$office."', office_hours='".$office_hours."' WHERE courseid='".$courseid."'";
	$stmt = $db->prepare($query);
	$stmt->execute();
	echo "Instructor widget information has been successfully updated! Refresh to see the changes.";
	}

?>