<?php 
    $pageTitle = "Join";
    include "includes/directory-top.php";
    include "includes/join-functions.php";
?>

<h1>Join</h1>
<?php echo $msg; ?>

<p>To add your link to the directory, fill in the form below. Use the <a href="update.php">update form</a> if you need to update your information, as you won't be able to submit anything here.</p>

<p>Name, email, website title/URL, content rating, and country are required. (Your email address is only used to retrieve your records and communicate with you about your status - it won't be shown on the directory.) You can optionally provide a button link and a brief "elevator pitch" about your website if you'd like. <?php if($useTags) { echo "Tags are optional, but you won't show up in any tag searches if you don't choose any."; } ?></p>

<form method="post" action="join.php">
<div><label for="username">Name</label><br/><input type="text" name="username" placeholder="Name" value="<?php echo $_SESSION['name']; ?>"></input></div>

<div><label for="email">Email</label><br/><input type="text" name="email" placeholder="name@domain.com" value="<?php echo $_SESSION['email']; ?>"></input></div>

<div><label for="title">Website title</label><br/><input type="text" name="title" placeholder="Title" value="<?php echo $_SESSION['title']; ?>"></input></div>

<div><label for="url">Website URL</label><br/><input type="text" name="url" placeholder="http://" value="<?php echo $_SESSION['url']; ?>"></input></div>

<div><label for="mature">Does your website contain adult content?</label><br/>
<input type="radio" id="matureyes" name="mature" value="yes" /> <label for="matureyes">Yes</label>
<input type="radio" id="matureno" name="mature" value="no" /> <label for="matureno">No</label></div>

<div><label for="country">Country</label><br /><select name="country"><option value="null">Please select a country:</option><?php listCountries($_SESSION['country']); ?></select></div>

<div><label for="button">Button URL (optional)</label><br/><input type="text" name="button" placeholder="http://" value="<?php echo $_SESSION['button']; ?>"></input></div>

<?php if($useTags) { ?>
<div><label for="tags[]">Website tags (optional)</label><br/>
<?php listTags($tagList, $_SESSION['tags']); ?></div>
<?php } ?>

<div><label for="comment">Elevator pitch (optional)</label><br/>
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
