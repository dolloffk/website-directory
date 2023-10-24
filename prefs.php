<?php
// Directory information
$directoryTitle = "My directory";
$directoryAddress = "http://domain.com"; // No trailing slash

// Admin login information
$username = "admin";
// The following should be a hashed password generated using hasher.php. Escape all dollar signs and make sure there's no spaces. Delete hasher.php from your server after you do this!!!!
$password_hash = "\$2y\$10\$EUnJSAcmgA5f0.UI3YiLee22OedojMo2v0MHcnV6DNCUtXewmhyLy";

// Admin email information. Specify if you'd like to receive an email when someone joins (true) or not (false)
$adminMail = "email@domain.com";
$emailAdmin = true;

// Email messages. Enter false (no quotes) if you don't want to email people when they join, when they're approved, or when their updates have been approved. Use \r\n for a new line. Details will appear underneath your message. $directoryTitle and $directoryAddress can be used for the title/URL of your directory.
$joinEmail = "Thank you for joining $directoryTitle. Your details are listed below. If you don't hear back from me in two weeks, feel free to email to check up on your application! \r\n\r\n";
$approvedEmail = "Thank you for joining $directoryTitle. You've been moved from the queue to the members list with the following details. If you need to update your information, you can use the update form located here: $directoryAddress/update.php \r\n\r\n";
$updateEmail = "Your updated information at $directoryTitle has been processed. Here is a copy of your new information for your records. If you need to update your information again, you can use the update form located here: $directoryAddress/update.php \r\n\r\n";

// Text to display with no members
$noMembers = "<p>No members have joined yet. The first one could be you!</p>";

// Members per page
$perPage = 20;

// Member sorting ("title" (website title), "date_ascending", "date_descending")
$sorting = "title";

// Member tagging. If useTags is set to false, tagList can be empty. Keep tags brief. Use dashes and not spaces to separate words.
$useTags = true;
$tagList = ["tag1", "tag2", "tag3", "tag4"];

// Date formatting (see php.net/date)
$timeFormat = "d F Y";

?>