<?php
    function toArray($filepath) {
        $rows = [];
        $file = fopen($filepath, "r");
        while ($row = fgetcsv($file)) {
            $rows[] = $row;
        }
        fclose($file);

        $headers = array_shift($rows);
        $entries = [];
        foreach ($rows as $row) {
            $entries[] = array_combine($headers, $row);
        }
        
        return $entries;
    }
    
    function countMembers($members) {
        $totalMembers = count($members);
        return $totalMembers;
    }
    
    function getID($filepath) {
        $entries = toArray($filepath);
        $ids = array_column($entries, 'id');
        
        if (count($ids) > 0) {
            $id = max($ids) + 1; 
        } else {
            $id = 1;
        }
        return $id;
    }
    
    function sortMembers($members, $sorting) {
        if ($sorting == "title") {
            $titles = array_map('strtolower', array_column($members, 'title'));
            array_multisort($titles, SORT_ASC, $members);
        } elseif ($sorting == "date_ascending") {
            array_multisort(array_column($members, 'date'), SORT_ASC, SORT_NATURAL, $members);
        } elseif ($sorting == "date_descending") {
            array_multisort(array_column($members, 'date'), SORT_DESC, SORT_NATURAL, $members);
        } else {
            exit("Invalid sorting option chosen. Please check your settings in prefs.php!");
        }
        return $members;
    }
    
    function newMember($members) {
        if (countMembers($members) > 0) {
            $newMembers = sortMembers($members, "date_descending");
            $new = $newMembers[0];
            $newTitle = $new['title'];
            $newUrl = $new['url'];
            echo "<a href=\"$newUrl\" target=\"_blank\">$newTitle</a>";
        } else {
            echo "No members have joined.";
        }
    }
    
    function lastUpdate($timeFormat) {
        $updates = toArray("updates.csv");
        
        if (count($updates) > 0) {
            array_multisort(array_column($updates, 'date'), SORT_DESC, $updates);
            $lastUpdate = $updates[0];
            $date = date_create($lastUpdate['date']);
            echo date_format($date, $timeFormat);
        } else {
            echo "No updates have been made.";
        }
    }
    
    function listCountries($selectedCountry) {
        try {
            $countries = file_get_contents("includes/countries.txt");
            $countryList = explode(",",$countries);
            foreach ($countryList as $country) {
                echo "<option value=\"$country\""; if ($selectedCountry == $country) { echo "selected=\"selected\""; } echo ">$country</option>";
            }
        } catch (Exception $ex) {
            echo "<p>There was an error listing the countries.</p>";
        }
    }
    
    function listTags($tagList, $selectedTags) {
        try {
            foreach($tagList as $tag) {
                if (isset($selectedTags)) {
                    $checked = in_array($tag, $selectedTags) ? "checked" : "";
                }
                echo "<div><input type=\"checkbox\" id=\"$tag\" value=\"$tag\" name=\"tags[]\" $checked /> <label for=\"$tag\">$tag</label></div>";
            }
        } catch (Exception $ex) {
            echo "<p>There was an error listing the tags.</p>";
        }
    }
    
	function showPages($page, $total_pages) {
        if ($total_pages > 0) { ?>
            <ul class="pages">
            <?php if ($page > 1) { echo "<li class=\"page\"><a href=\"?page=". $page-1 ."\">‹ Prev</a></li>"; } ?>
            <?php if ($page > 3) { echo "<li class=\"page\"><a href=\"?page=1\">1</a></li>"; echo "<li class=\"dots\">...</li>"; } ?>
            <?php if ($page-2 > 0) { echo "<li class=\"page\"><a href=\"?page=". $page-2 ."\">". $page-2 ."</a></li>"; } 
                  if ($page-1 > 0) { echo "<li class=\"page\"><a href=\"?page=". $page-1 ."\">". $page-1 ."</a></li>"; } ?>
            <li class="active"><?php echo $page ?></li>
            <?php if ($page+1 < $total_pages+1) { echo "<li class=\"page\"><a href=\"?page=". $page+1 ."\">". $page+1 ."</a></li>"; } 
                  if ($page+2 < $total_pages+1) { echo "<li class=\"page\"><a href=\"?page=". $page+2 ."\">". $page+2 ."</a></li>"; } ?>
            <?php if ($page < $total_pages-2) { echo "<li class=\"dots\">...</li>"; echo "<li class=\"page\"><a href=\"?page=". $total_pages ."\">". $total_pages ."</a></li>"; } ?>
            <?php if ($page < $total_pages) { echo "<li class=\"page\"><a href=\"?page=". $page+1 ."\">Next ›</a></li>"; } ?>
            </ul>
<?php }
	}
    
    function tagsToArray($tagString) {
        $tagArray = explode(" ", $tagString);
        return $tagArray;
    }
    
    function tagsToString($tagArray) {
        $tagString = implode(" ", $tagArray);
        return $tagString;
    }
    
    function displayTags($tags) {
        $tagArray = tagsToArray($tags);
        foreach($tagArray as $tag) {
            echo "<span class=\"tag\">$tag</span>";
        }
    }
    
    function displayMember($name, $email, $title, $url, $mature, $country, $button, $tags, $comment) {
        include "templates/member.php";
    }
    
    function filterMembers($members, $filterTags, $method) {
        $filteredMembers = [];
        foreach ($members as $member) {
            if (!empty($member['tags'])) {
                $memberTags = tagsToArray($member['tags']);
                if ($method == "and") {
                    if (array_intersect($filterTags, $memberTags) == $filterTags)  {
                        $filteredMembers[] = $member;
                    }
                } else if ($method == "or") {
                    if (count(array_intersect($filterTags, $memberTags)) > 0)  {
                        $filteredMembers[] = $member;
                    }
                }
            }
        }
        
        return $filteredMembers;
    }

    function checkName($name) {
        if (!empty($name) && !preg_match("/^[a-zA-Z-'\s]*$/", $name)) {
            return false;
        }
        return true;
    }
    
    function checkEmail($email) {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }
    
    function repeatEmail($email, $memberpath) {
        $members = toArray($memberpath);
        $key = array_search($email, array_column($members, 'email'));
        if ($key !== false) {
            return true;
        }
        return false;
    }
    
    function checkTitle($title) {
        if (!empty($title) && !preg_match("/^[a-zA-Z-'0-9\.\:\s]*$/", $title)) {
            return false;
        }
        return true;
    }
    
    function checkUrl($url) {
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
    }
    
    function checkButton($button) {
        if (!empty($button) && !checkUrl($button)) {
            return false;
        } elseif (!empty($button) && !preg_match("/^.*\.(jpg|png|gif|webp|svg)$/", strtolower($button))) {
            return false;
        }
        return true;
    }
    
    function isBot() {
        $bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

        foreach ($bots as $bot) {
            if (stripos($_SERVER['HTTP_USER_AGENT'], $bot)) {
                return true;
            }
        }

        if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ") {
            return true;
        }
        
        return false;
    }
    
    function checkComment($text) {
        $exploits = "/(content-type|bcc:|cc:|document.cookie|onclick|onload|alert)/i";
        if (isBot() || preg_match($exploits, $text) || preg_match("/(<.*>)/i", $text)) {
            return false;
        }
        
        return true;
    }
    
    function validateForm($name, $email, $title, $url, $button, $comment) {
        if (!checkName($name)) {
            $error .= "The name you provided isn't valid - it should only consist of letters.&nbsp;";
        }
        
        if (!checkEmail($email)) {
            $error .= "The email you provided isn't valid. &nbsp;";
        }
        
        if (!checkTitle($title)) {
            $error .= "The title you provided isn't valid. Allowed characters are letters, numbers, periods (.), dashes (-), apostrophes ('), and spaces.";
        }
        
        if (!checkUrl($url)) {
            $error .= "The URL you provided isn't valid.&nbsp;";
        }
        
        if(!checkButton($button)) {
            $error .= "The button link you provided isn't valid. Allowed extensions are jpg, png, gif, webp, and svg.";
        }
        
        if (!checkComment($comment)) {
            $error .= "No bots or HTML!&nbsp;";
        }
        
        if (substr_count($comment, 'http://') > 0) {
            $error .= "Please only include URLs in the website field.";
        }
        
        return $error;
    }
    
    function addMember($filepath, $name, $email, $title, $url, $mature, $country, $button, $tags, $comment) {
        try {
            $id = getID($filepath);
            $newMember = array($id, date("Y-m-d H:i:s"), $name, $email, $title, $url, $mature, $country, $button, $tags, $comment);
            
            $file = fopen($filepath,"a");
            fputcsv($file, $newMember);
            fclose($file);
            
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    function addMemberUpdate($oldemail, $name, $email, $title, $url, $mature, $country, $button, $tags, $comment) {
        try {
            $members = toArray("members.csv");
            $key = array_search($oldemail, array_column($members, 'email'));
            $newInfo = array(
                "name" => $name, 
                "email" => $email, 
                "title" => $title, 
                "url" => $url, 
                "mature" => $mature, 
                "country" => $country, 
                "button" => $button, 
                "tags" => $tags, 
                "comment" => $comment
            );
            
            $newMember = fillUpdate($key, $newInfo);
            
            $file = fopen("info-updates.csv","a");
            fputcsv($file, $newMember);
            fclose($file);
            
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    function fillUpdate($key, $newInfo) {
        $members = toArray("members.csv");
        $oldInfo = $members[$key];
        $newMember = array(
            "id" => $oldInfo['id'],
            "date" => $oldInfo['date']
        );
        
        foreach($newInfo as $field=>$newValue) {
            $newMember[$field] = empty($newValue) || $newValue == "null" || $newValue == null ? $oldInfo[$field] : $newValue;
        }
        
        return $newMember;
    }
    
    function sendEmail($subject, $message, $name, $email, $title, $url, $mature, $country, $button, $tags, $comment, $toMail, $adminMail) {
        if (!empty($tags)) {
            $message .= "Name: $name \r\nEmail: $email \r\nWebsite title: $title \r\nWebsite URL: $url \r\nMature content: $mature \r\nCountry: $country \r\nButton URL: $button \r\nTags: $tags \r\nElevator pitch: $comment";
        } else {
            $message .= "Name: $name \r\nEmail: $email \r\nWebsite title: $title \r\nWebsite URL: $url \r\nMature content: $mature \r\nCountry: $country \r\nButton URL: $button \r\nElevator pitch: $comment";
        }
                
        if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
            $headers = "From: $adminMail \n Reply-To: $adminMail";
        } else {
            $headers = "From: $directoryTitle <$adminMail> \n Reply-To: <$adminMail>";
        }
        
        mail($toMail, $subject, $message, $headers);
    }
?>