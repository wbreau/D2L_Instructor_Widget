<?php
	function database_connect()
	{
		$db = new mysqli('localhost', 'db_username', 'db_password', 'database');
		if (mysqli_connect_errno()) {
			echo '<p>Error: Could not connect to database.<br />
			Please try again later.</p>';
			$db = null;
			return $db;
		} else {
			return $db;
		}
	}
		
?>