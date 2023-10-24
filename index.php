<?php 
error_reporting(E_ALL);
    $pageTitle = "Home";
    include "includes/directory-top.php";
    $members = toArray("members.csv");
    $pending = toArray("queue.csv");
?>

<h1>Welcome!</h1>
<p>Welcome to <?php echo $directoryTitle; ?>!</p>

<p>
<strong>Websites:</strong> <?php echo countMembers($members); ?> (+<?php echo countMembers($pending); ?> pending)<br />
<strong>New</strong>: <?php newMember($members); ?><br/>
<strong>Created:</strong> 01 January 1901<br/>
<strong>Last updated:</strong> <?php lastUpdate($timeFormat); ?><br/>
<strong>Script:</strong> <a href="http://kalechips.net" target="_blank">Kalechips</a>
</p>

<?php        
    include "includes/directory-bottom.php";
?>
