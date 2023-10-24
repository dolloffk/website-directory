<?php

ob_start();
session_start();

$pageTitle = "Admin";
include "prefs.php";
include "includes/functions.php";
include "includes/admin-functions.php";
include "templates/top.php"; 
?>
<div class="wrapper">
<?php
$error = "";

// Display login form if not logged in
if (!isset($_SESSION['loggedin'])) {
    if (isset($_GET['p']) && $_GET['p'] == "login") {
        if (($_POST['username'] == $username) && (password_verify($_POST['password'], $password_hash))) {
            // Log in, start a new session, and redirect back to the admin panel
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            header('Location: admin');
        } else {
            $error = "<p>There was an error logging in. Try again.</p>";
        }
}
?>
    <h1>Admin panel login</h1>
    <?php echo $error; ?>
    <form action="?p=login" method="post">
    <p><label for="username">Name</label> <input type="text" name="username" id="username" required /><br />
    <label for="password">Password</label> <input type="password" name="password" id="password" required /><br />
    <input type="submit" id="submit"  value="Login" /></p></form>
<?php
} else {
?>

<?php 
    $headers = array("id","date","name","email","title","url","mature","country","button","tags","comment");
    $tab = $_GET['p'];
    if ($tab=="") { $tab = "home"; }
    
    switch($tab) {
        case "approvedmembers":
            $members = sortMembers(toArray("members.csv"), "date_descending");
?>
            <h1>Approved members</h1>
<?php
            if (countMembers($members) > 0) {
                showMembers($members, "approved");
            } else {
                echo "<p>No members to show.</p>";
            }
?>
            <p><a href="admin">Back to admin panel</a></p>
<?php
            break;
        case "pendingmembers":
            $members = sortMembers(toArray("queue.csv"), "date_descending"); 
?>
            <h1>Pending members</h1>
<?php
            if (countMembers($members) > 0) {
                showMembers($members, "pending");
            } else {
                echo "<p>No members to show.</p>";
            }
?>
            <p><a href="admin">Back to admin panel</a></p>
<?php
            break;
        case "pendingupdates":
            $members = sortMembers(toArray("info-updates.csv"), "date_descending"); 
?>
            <h1>Pending updates</h1>
<?php
            if (countMembers($members) > 0) {
                showMembers($members, "update");
            } else {
                echo "<p>No updates to show.</p>";
            }
?>
            <p><a href="admin">Back to admin panel</a></p>
<?php
            break;
        case "edit":
            if (isset($_GET['pending'])) {
                $member_id = $_GET['pending'];
                $members = toArray("queue.csv");
                $submittext = "Save and approve";
                $back = "pendingmembers";
            } elseif (isset($_GET['approved'])) {
                $member_id = $_GET['approved'];
                $members = toArray("members.csv");
                $submittext = "Save changes";
                $back = "approvedmembers";
            } elseif (isset($_GET['update'])) {
                $member_id = $_GET['update'];
                $members = toArray("info-updates.csv");
                $submittext = "Save changes";
                $back = "pendingupdates";
            }
            
            $key = array_search($member_id, array_column($members, 'id'));
            if ($key !== false) {
                $member = $members[$key];
        ?>
                <h1>Edit entry</h1>
                <form method="post" action="?p=editmember">
                <div><label for="name">Name</label><br/><input type="text" name="name" placeholder="Name" value="<?php echo $member['name']; ?>" /></div>

                <div><label for="email">Email</label><br/><input type="text" name="email" placeholder="name@domain.com" value="<?php echo $member['email']; ?>" /></div>

                <div><label for="title">Website title</label><br/><input type="text" name="title" placeholder="Title" value="<?php echo $member['title']; ?>" /></div>

                <div><label for="url">Website URL</label><br/><input type="text" name="url" placeholder="http://" value="<?php echo $member['url']; ?>" /></div>

                <div><label for="mature">Does your website contain adult content?</label><br/>
                <input type="radio" id="matureyes" name="mature" value="yes" <?php if ($member['mature']=="yes") { echo "checked"; } ?> /> <label for="matureyes">Yes</label>
                <input type="radio" id="matureno" name="mature" value="no" <?php if ($member['mature']=="no") { echo "checked"; } ?> /> <label for="matureno">No</label></div>

                <div><label for="country">Country</label><br /><select name="country"><option value="null">Please select a country:</option><?php listCountries($member['country']); ?></select></div>
                

                <div><label for="button">Button URL</label><br/><input type="text" name="button" placeholder="http://" value="<?php echo $member['button']; ?>" /></div>
                
                <?php if($useTags) { ?>
                <div><label for="tags[]">Website tags (optional)</label><br/>
                <?php listTags($tagList, tagsToArray($member['tags'])); ?></div>
                <?php } ?>

                <div><label for="comment">Elevator pitch (optional)</label><br/>
                <textarea rows="6" name="comment"><?php echo str_replace("<br />", "\n", $member['comment']); ?></textarea></div>
                
                <input type="hidden" name="date" value="<?php echo $member['date']; ?>" />
                <input type="hidden" name="member_id" value="<?php echo $member_id; ?>" />
                <input type="hidden" name="comment_key" value="<?php echo $key; ?>" />
                <input type="hidden" name="member_status" value="<?php echo $back; ?>" />

                <input type="submit" name="submit" value="<?php echo $submittext; ?>" />
                <input type="submit" name="delete" value="Delete" />
                </form>

                <p><a href="?p=<?php echo $back; ?>">Back to list</a></p>
<?php 
            } else {
?>
                <h1>Edit member</h1>
                <p>This member ID doesn't exist.</p>
                <p><a href="?p=<?php echo $back; ?>">Back to list</a></p>
<?php
            }
            break;
        case "editmember": ?>
                <h1>Edit member</h1>
<?php
            if (isset($_POST['submit'])) {
                $memberTags = $useTags ? tagsToString($_POST['tags']) : "";
                if ($_POST['member_status'] == "pendingmembers") {
                    if (deleteMember($_POST['member_id'], "queue.csv", $headers)) {
                        if (addMember("members.csv", $_POST['name'], $_POST['email'], $_POST['title'], $_POST['url'], $_POST['mature'], $_POST['country'], $_POST['button'], $memberTags, str_replace(array("\r\n", "\r", "\n"), "<br />", $_POST['comment']))) {
                            $updateDetails = "Approved new member:".$_POST['title'];
                            
                            if ($approvedEmail !== false) {
                                $subject = "Thank you for joining $directoryTitle";
                                $message = $approvedEmail;
                                sendEmail($subject, $message, $_POST['name'], $_POST['email'], $_POST['title'], $_POST['url'], $_POST['mature'], $_POST['country'], $_POST['button'], $_POST['comment'], $_POST['email'], $adminMail);
                            }
                            
                            addUpdate($updateDetails);
                            echo "<p>Member approved successfully.</p>";
                        } else {
                            echo "<p>An error occurred processing this request.</p>";
                        }
                    } else {
                        echo "<p>An error occurred processing this request.</p>";
                    }
                } elseif ($_POST['member_status'] == "approvedmembers") {
                    if (updateMember($_POST['member_id'], $_POST['date'], $_POST['name'], $_POST['email'], $_POST['title'], $_POST['url'], $_POST['mature'], $_POST['country'], $_POST['button'], $memberTags, str_replace(array("\r\n", "\r", "\n"), "<br />", $_POST['comment']), $headers)) {
                        $updateDetails = "Edited member:".$_POST['title'];
                        addUpdate($updateDetails);
                        echo "<p>Member edited successfully.</p>";
                    } else {
                        echo "<p>An error occurred processing this request.</p>";
                    }
                } elseif ($_POST['member_status'] == "pendingupdates") {
                    if (deleteMember($_POST['member_id'], "info-updates.csv", $headers)) {
                        if (updateMember($_POST['member_id'], $_POST['date'], $_POST['name'], $_POST['email'], $_POST['title'], $_POST['url'], $_POST['mature'], $_POST['country'], $_POST['button'], $memberTags, str_replace(array("\r\n", "\r", "\n"), "<br />", $_POST['comment']), $headers)) {
                            $updateDetails = "Updated member:".$_POST['title'];
                            
                            if ($updateEmail !== false) {
                                $subject = "Information updated at $directoryTitle";
                                $message = $updateEmail;
                                sendEmail($subject, $message, $_POST['name'], $_POST['email'], $_POST['title'], $_POST['url'], $_POST['mature'], $_POST['country'], $_POST['button'], $memberTags, $_POST['comment'], $_POST['email'], $adminMail);
                            }
                            
                            addUpdate($updateDetails);
                            echo "<p>Update approved successfully.</p>";
                        } else {
                            "<p>An error occurred processing this request.</p>";
                        }
                    } else {
                        echo "<p>An error occurred processing this request.</p>";
                    }
                }
?>
                <p><a href="?p=<?php echo $_POST['member_status']; ?>">Back to list of members</a></p>
<?php
            } elseif (isset($_POST['delete'])) {
                if ($_POST['member_status'] == "pendingmembers") {
                    if (deleteMember($_POST['member_id'], "queue.csv", $headers)) {
                        addUpdate("Deleted member");
                        echo "<p>Member deleted successfully.</p>";
                    } else {
                        echo "<p>An error occurred processing this request.</p>";
                    }
                } elseif ($_POST['member_status'] == "approvedmembers") {
                    if (deleteMember($_POST['member_id'], "members.csv", $headers)) {
                        addUpdate("Deleted member");
                        echo "<p>Member deleted successfully.</p>";
                    } else {
                        echo "<p>An error occurred processing this request.</p>";
                    }
                } elseif ($_POST['member_status'] == "pendingupdates") {
                    if (deleteMember($_POST['member_id'], "info-updates.csv", $headers)) {
                        addUpdate("Deleted update");
                        echo "<p>Update deleted successfully.</p>";
                    } else {
                        echo "<p>An error occurred processing this request.</p>";
                    }
                }
                
?>
                <p><a href="?p=<?php echo $_POST['member_status']; ?>">Back to list of members</a></p>
<?php
            } else {
?>
                <p>Oops! You must have gotten here by mistake.</p>
                <p><a href="admin">Back to admin panel</a></p>
<?php
            }
            break;
        case "home": ?>
            <h1>Admin</h1>
            <ul>
                <li><a href="?p=approvedmembers">Approved members</a></li>
                <li><a href="?p=pendingmembers">Pending members</a></li>
                <li><a href="?p=pendingupdates">Pending updates</a></li>
                <li><a href="index">Directory home</a></li>
            </ul>

            <a href="?p=logout">Logout</a>
<?php
            break;
        case "logout":
            session_destroy();
            header('Location: admin');
            break;
    }
?>

<?php } ?>
</div>
<?php include "templates/bottom.php"; ?>