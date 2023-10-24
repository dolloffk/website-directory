<?php
    $_SESSION['name'] = null;
    $_SESSION['email'] = null;
    $_SESSION['title'] = null;
    $_SESSION['url'] = null;
    $_SESSION['country'] = null;
    $_SESSION['button'] = null;
    $_SESSION['tags'] = null;
    $_SESSION['comment'] = null;

    $msg = null;

    if (isset($_GET['p']) && $_GET['p'] == "success") {
        $msg .= "<p><strong>Form successfully submitted for approval!</strong>&nbsp;";
        if ($updateEmail !== false) {
            $msg .= "You will receive an email when your update has been processed.</p><hr/>";
        } else {
            $msg .= "</p><hr/>";
        }
    }

    if (isset($_POST['submit'])) {
        $error = null;
        
        if (!empty($_POST['name']) || !empty($_POST['website']) || isBot()) {
            $error .= "No bots! ";
        }
        
        $name = strip_tags($_POST['username']);
        $oldemail = strip_tags($_POST['oldemail']);
        $newemail = strip_tags($_POST['newemail']);
        $title = strip_tags($_POST['title']);
        $url = strip_tags($_POST['url']);
        $mature = $_POST['mature'];
        $country = $_POST['country'];
        $button = strip_tags($_POST['button']);
        $tags = $useTags ? tagsToString($_POST['tags']) : "";
        $comment = htmlentities(strip_tags($_POST['comment']));
        
        $_SESSION['name'] = $name;
        $_SESSION['oldemail'] = $oldemail;
        $_SESSION['newemail'] = $newemail;
        $_SESSION['title'] = $title;
        $_SESSION['url'] = $url;
        $_SESSION['country'] = $country;
        $_SESSION['button'] = $button;
        $_SESSION['tags'] = $_POST['tags'];
        $_SESSION['comment'] = $comment;
        
        if (empty($oldemail)) {
            $error .= "Current email is a required field.&nbsp;";
        } elseif (!repeatEmail($oldemail, "members.csv") && !repeatEmail($oldemail, "queue.csv")) {
            $error .= "Your email isn't in the members list or queue. Please use the join form to join the directory.";
        } elseif (repeatEmail($oldemail, "queue.csv")) {
            $error .= "Your email is already in the member queue. If you want to update something, email me!";
        } else {
            $error .= validateForm($name, $newemail, $title, $url, $button, $comment);
        }
        
        if ($error == null) {
            $email = !empty($newemail) ? $newemail : $oldemail;
            
            if ($emailAdmin) {
                $subject = "Member update at $directoryTitle";
                $message = "A member at $directoryTitle has requested to update their information with the following details. You can approve them at $directoryAddress/admin.php?p=pendingupdates. \r\n\r\n";
                sendEmail($subject, $message, $name, $email, $title, $url, $mature, $country, $button, $tags, $comment, $adminMail, $adminMail);
            }
            
            if (addMemberUpdate($oldemail, $name, $email, $title, $url, $mature, $country, $button, $tags, str_replace(array("\r\n", "\r", "\n"), "<br />", $comment))) {
                $_SESSION['name'] = null;
                $_SESSION['oldemail'] = null;
                $_SESSION['newemail'] = null;
                $_SESSION['title'] = null;
                $_SESSION['url'] = null;
                $_SESSION['country'] = null;
                $_SESSION['button'] = null;
                $_SESSION['tags'] = null;
                $_SESSION['comment'] = null;
                flush();
                header("Location: update.php?p=success");
            } else {
                $msg .= "<p>There was an error submitting your application. This is an issue on the server end, so please let the admin know!</p>";
            }
        } else {
            $msg .= "<p><strong>Your form couldn't be processed.</strong> See the following errors:</p><p>$error</p><hr/>";
        }
    }
?>