Instructor Widget LTI Tool
---------------------

This widget allows instructors to quickly and easily add a picture of themselves and information such as name, email address, phone number, office number, office hours, and a brief bio to their course home page. The widget uses the course OU number to query a database on the remote site. If the OU exists as a primary key in the database, the widget displays the instructor's information to the students or the information plus an edit button to the instructor. If it does not already exist in the database, then the students would see an "under construction" message and the instructor would see the form to complete with their information.

To install, copy these files to the root directory of your webserver and change the variable `$SITE_URL` in `instinfo.php` to match your server name (if it is on a weird port, use syntax like `https://example.com:1234`). It's important to place the code on an SSL enabled site to avoid mixed content warnings and to ensure that the widget displays correctly. 

A mysql database must also be created on the remote web server side to hold the information.

The LTI launch point for the widget is `instinfo.php`
