<?php
include 'database/database.php';
session_start();
if(isset($_SESSION['context_id'])) {
    $courseid = $_SESSION['context_id'];
    }

$target_dir = "images/".$courseid."";
$target_file = $target_dir . basename($_FILES["image"]["name"]);	
$instr_name = $_POST['instr_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$bio = $_POST['bio'];
$office = $_POST['office'];
$office_hours = $_POST['office_hours'];
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

$image = $courseid.$_FILES["image"]["name"];

// Check if file already exists
if (file_exists($target_file)) {
    echo "<p>Sorry, an image file with the name " .$image. " already exists. Please rename your image file and try again.</p>";
    $uploadOk = 0;
} else {			
$db = database_connect();
if($db != null) {
	$query = "INSERT INTO instructor_info (courseid, inst_name, email, phone, bio, image, office, office_hours) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
	$stmt = $db->prepare($query);
	$stmt->bind_param('ssssssss', $courseid, $instr_name, $email, $phone, $bio, $image, $office, $office_hours);
	$stmt->execute();
				
	if ($stmt->affected_rows > 0) {
		echo "<p>Instructor Widget has been updated. Refresh the page to see the changes.</p>";
		if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

	} else {
		echo "<p>An error has occurred.<br/>
		Please contact the I.T. Department for further assistance.</p>";
	}
}
$db->close();
}
?>