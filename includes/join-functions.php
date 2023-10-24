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
        if ($joinEmail !== false) {
            $msg .= "You should receive an email with your details shortly.</p><hr/>";
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
        $email = strip_tags($_POST['email']);
        $title = strip_tags($_POST['title']);
        $url = strip_tags($_POST['url']);
        $mature = $_POST['mature'];
        $country = $_POST['country'];
        $button = strip_tags($_POST['button']);
        $tags = $useTags ? tagsToString($_POST['tags']) : "";
        $comment = htmlentities(strip_tags($_POST['comment']));
        
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['title'] = $title;
        $_SESSION['url'] = $url;
        $_SESSION['country'] = $country;
        $_SESSION['button'] = $button;
        $_SESSION['tags'] = $_POST['tags'];
        $_SESSION['comment'] = $comment;
        
        
        
        if (empty($name) || empty($email) || empty($title) || empty($url) || empty($mature) || empty($country)) {
            $error .= "Name, email, website title, website URL, content rating, and country are required fields.&nbsp;";
        } else {
            if (repeatEmail($email, "members.csv")) {
                $error .= "Your email is already on the member list. Please use the update form if you need to update your information.";
            } elseif (repeatEmail($email, "queue.csv")) {
                $error .= "Your email is already in the member queue. If you want to update something, email me!";
            } else {
                $error .= validateForm($name, $email, $title, $url, $button, $comment);
            }
            
            if ($error == null) {
                $csvpath = "queue.csv";
                
                if ($joinEmail !== false) {
                    $subject = "Thank you for joining $directoryTitle";
                    $message = $joinEmail;
                    sendEmail($subject, $message, $name, $email, $title, $url, $mature, $country, $button, $tags, $comment, $email, $adminMail);
                }
                
                if ($emailAdmin) {
                    $subject = "New member at $directoryTitle";
                    $message = "A new member has joined $directoryTitle with the following details. You can approve them at $directoryAddress/admin.php?p=pending. \r\n\r\n";
                    sendEmail($subject, $message, $name, $email, $title, $url, $mature, $country, $button, $tags, $comment, $adminMail, $adminMail);
                }
                
                if (addMember($csvpath, $name, $email, $title, $url, $mature, $country, $button, $tags, str_replace(array("\r\n", "\r", "\n"), "<br />", $comment))) {
                    $_SESSION['name'] = null;
                    $_SESSION['email'] = null;
                    $_SESSION['title'] = null;
                    $_SESSION['url'] = null;
                    $_SESSION['country'] = null;
                    $_SESSION['button'] = null;
                    $_SESSION['tags'] = null;
                    $_SESSION['comment'] = null;
                    flush();
                    header("Location: join.php?p=success");
                } else {
                    $msg .= "<p>There was an error submitting your application. This is an issue on the server end, so please let the admin know!</p>";
                }
            } else {
                $msg .= "<p><strong>Your form couldn't be processed.</strong> See the following errors:</p><p>$error</p><hr/>";
            }
        }
    }
?>