<?php 
    $pageTitle = "Update";
    include "includes/directory-top.php";
    include "includes/update-functions.php";
?>

<h1>Update</h1>
<?php echo $msg; ?>

<p>Use this form to update your information. It uses your email address to look you up, so you'll need to input that even if you plan on updating your email. Besides that, you only need to input information for what you'd like to change; the system will autofill in all your old information for empty fields.</p>

<p>Updates will be manually approved. If there are drastic changes, you may be contacted to verify it was you who submitted them.</p>

<form method="post" action="update.php">
<div><label for="oldemail">Current email (required)</label><br/><input type="text" name="oldemail" placeholder="name@domain.com" value="<?php echo $_SESSION['email']; ?>"></input></div>

<div><label for="username">New name</label><br/><input type="text" name="username" placeholder="Name" value="<?php echo $_SESSION['name']; ?>"></input></div>

<div><label for="newemail">New email</label><br/><input type="text" name="newemail" placeholder="name@domain.com" value="<?php echo $_SESSION['email']; ?>"></input></div>

<div><label for="title">New website title</label><br/><input type="text" name="title" placeholder="Title" value="<?php echo $_SESSION['title']; ?>"></input></div>

<div><label for="url">New website URL</label><br/><input type="text" name="url" placeholder="http://" value="<?php echo $_SESSION['url']; ?>"></input></div>

<div><label for="mature">Does your website contain adult content?</label><br/>
<input type="radio" id="matureyes" name="mature" value="yes" /> <label for="matureyes">Yes</label>
<input type="radio" id="matureno" name="mature" value="no" /> <label for="matureno">No</label></div>

<div><label for="country">New country</label><br /><select name="country"><option value="null">Please select a country:</option><?php listCountries($_SESSION['country']); ?></select></div>

<div><label for="button">New button URL</label><br/><input type="text" name="button" placeholder="http://" value="<?php echo $_SESSION['button']; ?>"></input></div>

<?php if($useTags) { ?>
<div><label for="tags[]">New website tags</label><br/>
<?php listTags($tagList, $_SESSION['tags']); ?></div>
<?php } ?>

<div><label for="comment">New elevator pitch</label><br/>
<textarea rows="6" name="comment"><?php echo $_SESSION['comment']; ?></textarea></div>

<!-- spambot traps -->
<div style="display:none;">
<input type="text" name="name" />
<input type="text" name="website" />
</div>

<input type="submit" name="submit" value="Submit" />

<?php 
    include "includes/directory-bottom.php";
?>
