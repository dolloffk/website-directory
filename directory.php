<?php 

    $pageTitle = "Directory";
    include "includes/directory-top.php";

    $filepath = "members.csv";
    
    if ($useTags == false) {
        $members = sortMembers(toArray($filepath), $sorting);
    } elseif($useTags == true) {
        if (isset($_GET['tags']) && isset($_GET['method'])) {
            if ($_GET['method'] == "and" || $_GET['method'] == "or") {
                $memberArray = toArray($filepath);
                $members = sortMembers(filterMembers($memberArray, $_GET['tags'], $_GET['method']), $sorting);
                $filterMessage = "Viewing members with tags: ";
                foreach ($_GET['tags'] as $tag) {
                    $filterMessage .= "<span class=\"tag\">$tag</span>";
                }
            } else {
                echo "You haven't picked a valid sorting method.";
                exit;
            }
        } else {
            $members = sortMembers(toArray($filepath), $sorting);
        }
    } else {
        echo "You don't have a valid value set for useTags in prefs.php. It should be true or false (no quotes!)";
        exit;
    }

    $totalMembers = countMembers($members);
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $totalPages = ceil($totalMembers/$perPage);
    $pageStart = ($page - 1) * $perPage;
    $pageMembers = array_slice($members, $pageStart, $perPage);
?>
<h1>Directory</h1>
<?php if ($useTags) { 
    echo "<p>$filterMessage</p>";
?>
    <details>
    <summary>Filter by tag</summary>
    <form action="directory.php">
    <div><label for="tags[]">Select tags to filter:</label><br/>
    <?php listTags($tagList, $_GET['tags']); ?></div>
    <div><label for="method">Method: ("and" stacks filters, while "or" selects members with any selected tag)</label><br/>
    <input type="radio" id="and" name="method" value="and" <?php if ($_GET['method'] == "and" || !isset($_GET['method'])) { echo "checked"; } ?> /> <label for="and">And</label>
    <input type="radio" id="or" name="method" value="or" <?php if ($_GET['method'] == "or") { echo "checked"; } ?> /> <label for="or">Or</label></div>
    <input type="submit" value="Submit filter" />
    </form>
    </details>
<?php } 

    if ($totalMembers == 0) {
        echo $noMembers;
    } else {
        if ($totalPages > 1) { showPages($page,$totalPages); }
        foreach ($pageMembers as $member) {
            $name = $member['name'];
            $email = $member['email'];
            $title = $member['title'];
            $url = $member['url'];
            $mature = $member['mature'];
            $country = $member['country'];
            $button = $member['button'];
            $tags = $member['tags'];
            $comment = $member['comment'];
            displayMember($name, $email, $title, $url, $mature, $country, $button, $tags, $comment);
        }
        if ($totalPages > 1) { showPages($page,$totalPages); }
    }
        
    include "includes/directory-bottom.php";
?>
