<div class="member">
<div class="url"><a href="<?php echo $url; ?>" target="_blank"><?php echo $title; ?>
<?php if (stripos($button, "http") !== false) { ?> <br/><img src="<?php echo $button; ?>" /><?php } ?></a></div>
<div class="name"><?php echo $country; ?><?php if ($mature == "yes") { ?><br/><span class="mature">mature</span><?php } ?></div>
<?php if (!empty($comment)) { ?><p class="blurb"><?php echo $comment; ?></p><?php } ?>
<?php if (!empty($tags)) { ?><p class="tags"><?php displayTags($tags); // Use this function to list tags nicely ?></p><?php } ?>
</div>