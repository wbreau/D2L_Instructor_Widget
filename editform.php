<?php
include 'database/database.php';
session_start();
if(isset($_SESSION['context_id'])) {
    $courseid = $_SESSION['context_id'];
    }

	    ?>
<link rel="stylesheet" href="css/style.css">
<?php
$db = database_connect();

if($db != null) {
	$query = "SELECT * FROM instructor_info WHERE courseid = '".$courseid."'";
	$stmt = $db->prepare($query);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($courseid, $inst_name, $email, $phone, $bio, $image, $office, $office_hours);
	
	while($stmt->fetch()) {
	}
	$stmt->free_result();
	$db->close();
} else {
	echo "<p>The database could not be contacted or the information could not be retrieved. Please try closing your web browser and reopening it. If the problem persists, please contact the Technology Assistance Center.</p>";
}

?>
<form action="updateform.php" method="post">
<p><strong><label for="inst_name">Name:</label></strong><br/>
	<input type="text" name="inst_name" id="inst_name" size="40" value="<?=$inst_name;?>" /></p>
<p><strong><label for="email">Email:</label></strong><br/>
	<input type="text" name="email" id="email" size="40" value="<?=$email;?>" /></p>
<p><strong><label for="phone">Phone:</label></strong><br/>
	<input type="text" name="phone" id="phone" size="40" value="<?=$phone;?>" /></p>
<p><strong><label for="office">Office:</label></strong><br/>
	<input type="text" name="office" id="office" size="40" value="<?=$office;?>" /></p>
<p><strong><label for="office_hours">Office Hours:</label></strong><br/>
	<textarea name="office_hours" id="office_hours" rows="10" cols="50" /><?=$office_hours;?></textarea></p>
<p><strong><label for="bio">Bio:</label></strong><br/>
	<textarea name="bio" id="bio" rows="10" cols="50" /><?=$bio;?></textarea></p>
<p><input type="submit" name="update" value="Save Info" /></p>
</form>
<?php
	
?>