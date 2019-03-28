<?php
	
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

require_once 'util/lti_util.php';
require_once 'OAuth1p0.php';
include 'database/database.php';

/*
 *   CONFIGURATION
 *	 Create a key and secret that will be updated in the fields below inside of the single quotes. 
 *	 The site URL should be just the URL and not the complete path to the target page. 
 *	 The complete URL with target page will be entered inside of D2L Brightspace when setting up the remote plugin.
 */
$OAUTH_KEY    = 'key';
$OAUTH_SECRET = 'secret'; // You should use a better secret! This is shared with the LMS
$SITE_URL     = 'https://site_your_code_is_hosted_on.com';

if(!isset($_REQUEST['lis_outcome_service_url'])
|| !isset($_REQUEST['lis_result_sourcedid'])
|| !isset($_REQUEST['oauth_consumer_key'])
) {
	    // If these weren't set then we aren't a valid LTI launch
    // The only case this happens is on the redirect in gradeSubmit.php
	if(!isset($courseid)) {
        exit('This page was not launched via LTI from Brightspace. Make sure to launch the instructor widget from within Brightspace.');
    }
} else {
    // Ok, we are an LTI launch.

    /*
     *   VERIFY OAUTH SIGNATURE
     */

    // The LMS gives us a key and we need to find out which shared secret belongs to that key.
    // The key & secret are configured on the LMS in "Admin Tools" > "External Learning Tools" > "Manage Tool Providers",
    // Or in the remote plugin setup page if you are using them.
    $oauth_consumer_key = $_REQUEST['oauth_consumer_key'];

    // We only have one key, "key", which corresponds to the (shared) secret "secret"
    if($oauth_consumer_key != $OAUTH_KEY) {
        exit("If you are seeing this message, something isn't quite right. Try restarting your web browser and contact the Technology Assistance Center if you continue to see this message.");
    } else {
        $oauth_consumer_secret = $OAUTH_SECRET;
    } 

    /*if (!OAuth1p0::CheckSignatureForFormUrlEncoded($SITE_URL . $_SERVER['REQUEST_URI'], 'POST', $_POST, $oauth_consumer_secret)) {
        exit("If you are seeing this message, something isn't quite right. Try restarting your web browser and contact the Technology Assistance Center if you continue to see this message.");
    }*/

    // Store things that Instructor Widget will need into the session
    session_start();
    $_SESSION['lis_outcome_service_url'] = $_REQUEST['lis_outcome_service_url'];
    $_SESSION['lis_result_sourcedid']    = $_REQUEST['lis_result_sourcedid'];
    $_SESSION['lis_person_name_given']   = $_REQUEST['lis_person_name_given'];
    $_SESSION['context_id']   			 = $_REQUEST['context_id'];
    $_SESSION['ext_d2l_role']   		 = $_REQUEST['ext_d2l_role'];
    $_SESSION['oauth_consumer_key']      = $_REQUEST['oauth_consumer_key'];
    $_SESSION['oauth_consumer_secret']   = $oauth_consumer_secret;
    session_write_close();
    
    ?>
<link rel="stylesheet" href="css/style.css">
<?php

}

/*
 *   OPTIONAL LTI LAUNCH PARAMETERS
 *   Some LTI parameters are set by the LMS but may or may not be sent depending on security settings.
 *   One example is the user's given name. Check External Learning Tools to see if this is enabled for your links
 *   or disabled globally.
 *	 The following parameters provide the viewing user's first name, course OU number, and viewing user's role.
 */
if(isset($_REQUEST['lis_person_name_given'])) {
    $user = $_REQUEST['lis_person_name_given'];
}

if(isset($_REQUEST['context_id'])) {
    $courseid = $_REQUEST['context_id'];
}
if(isset($_REQUEST['ext_d2l_role'])) {
	$role = $_REQUEST['ext_d2l_role'];
}

/*
 * DATABASE CONNECTION AND QUERY 
 * The following code connects to the database using the database/database.php file. 
 * It then queries the database to see if the course ou exists as a primary key in the database.
 * If it does, it binds each element in the row to a variable that can be used later. 
 */
$db = database_connect();
if($db != null) {
	$query = "SELECT * FROM instructor_info WHERE courseid = '".$courseid."'";
	$stmt = $db->prepare($query);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($courseiddb, $inst_name, $email, $phone, $bio, $image, $office, $office_hours);
	
	while($stmt->fetch()) {

	}
	$stmt->free_result();
	$db->close();
} else {
	echo "<p>The database could not be contacted or the information could not be retrieved. Please try closing your web browser and reopening it. If the problem persists, please contact the I.T. Department.</p>";
}

/*
 * If the viewing user's role is one of the following and the courseid is null, show an "under construction" message.
 * If the viewing user's role is one of the following and the courseid exists, show the instructor information from the database.
 */
if($role == Student || $role == Tutor || $role == Evaluator || $role == Prospect || $role == Incomplete || $role == Interpreter) {
	if($courseiddb == null){
		echo "<h1>Your instructor should have their contact information posted here soon.</h1>";
	} else {
		echo "<h3><center>Welcome To Class, ".$user."!</center></h3>";
		if (!empty($image) and $image != $courseid) { echo "<center><img src='images/".$image."' width='300'></center>"; }
		if (!empty($inst_name)) { echo "<p>My name is " .$inst_name. ", and I will be your instructor for this course."; }
		if (!empty($email)) { echo "<p>If you need to reach me by email, my email address is <a href='mailto:".$email."'>".$email."</a>.</p>"; }
		if (!empty($phone)) { echo "<p>You can also reach me by phone at " .$phone. ".</p>"; }
		if (!empty($office)) { echo "<p>My office is located in Room " .$office. ".</p>"; }
		if (!empty($office_hours)) { echo "<p>My office hours are " .$office_hours. "</p>"; }
		if (!empty($bio)) { echo "<p>" .$bio. "</p>"; }
	}
} else {
/*
 * If the viewing user's role is any role other than those listed above and the courseid is null, show the form to add information to the database.
 * If the viewing user's role is any role other than those listed above and the courseid exists, show the instructor information from the database with an edit button to allow changes to be made to the information.
 */
	if($courseiddb == null) {
		echo "<h1>Please fill out the form to setup your instructor widget.</h1>";
		?>
		<form action="processform.php" method="post" enctype="multipart/form-data">
			<p><strong><label for="image">Upload A Friendly Picture</label></strong><br>
			Recommended image width is at least 300px.</p>
			<input type="file" name="image" accept="image/*">
			<p><strong><label for="instr_name">Name:</label></strong><br/>
				<input type="text" name="instr_name" id="instr_name" size="40" /></p>
			<p><strong><label for="email">Email:</label></strong><br/>
				<input type="text" name="email" id="email" placeholder="Enter your full email address please." size="40" /></p>
			<p><strong><label for="phone">Phone:</label></strong><br/>
				<input type="text" name="phone" id="phone" placeholder="406-771-5555" size="40" /></p>
			<p><strong><label for="office">Office:</label></strong><br/>
				<input type="text" name="office" id="office" Placeholder="Enter your office number, office location, etc." size="40" /></p>
			<p><strong><label for="office_hours">Office Hours:</label></strong><br/>
				<textarea name="office_hours" id="office_hours" rows="10" cols="50" /></textarea></p>
			<p><strong><label for="bio">Bio:</label></strong><br/>
				<textarea name="bio" id="bio" rows="10" cols="50" /></textarea></p>
			<p><input type="submit" value="Save Info" /></p>
		</form>
		<?php
	} else {
		echo "<h3><center>Welcome To Class, ".$user."!</center></h3>";
		if (!empty($image) and $image != $courseid) { echo "<center><img src='images/".$image."' width='300' alt='Picture of instructor, ".$inst_name.".' title='Picture of ".$inst_name."'></center>"; }
		if (!empty($inst_name)) { echo "<p>My name is " .$inst_name. ", and I will be your instructor for this course."; }
		if (!empty($email)) { echo "<p>If you need to reach me by email, my email address is <a href='mailto:".$email."'>".$email."</a>.</p>"; }
		if (!empty($phone)) { echo "<p>You can also reach me by phone at " .$phone. ".</p>"; }
		if (!empty($office)) { echo "<p>My office is located in Room " .$office. ".</p>"; }
		if (!empty($office_hours)) { echo "<p>My office hours are " .$office_hours. "</p>"; }
		if (!empty($bio)) { echo "<p>" .$bio. "</p>"; }
		?>
		<div><center><a class="button" href="editform.php">Edit</a></center></div>
		<br/>
		<div><center><a class="button" href="deleteinfo.php">Delete Info</a></center></div>
			 <?php

	}
}

?>