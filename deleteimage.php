<?php
session_start();
if(isset($_SESSION['context_id'])) {
    $courseid = $_SESSION['context_id'];
    }
include 'database/database.php';

$db = database_connect();
if($db != null) {
	$query = "SELECT image FROM instructor_info WHERE courseid = '".$courseid."'";
	$stmt = $db->prepare($query);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($image);
	
	while($stmt->fetch()) {

	}
	$stmt->free_result();
	$db->close();
} else {
	echo "<p>The database could not be contacted or the information could not be retrieved. Please try closing your web browser and reopening it. If the problem persists, please contact your I.T. Department for assistance.</p>";
}

array_map('unlink', glob("images/".$image.""));

$db = database_connect();
if($db != null) {
	$query = "UPDATE instructor_info SET image = NULL WHERE courseid='".$courseid."'";
	$stmt = $db->prepare($query);
	$stmt->execute();
	echo "Instructor widget information has been successfully updated! Refresh to see the changes.";
	}
?>