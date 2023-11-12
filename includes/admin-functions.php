<?php 
    function showMembers($members, $status) {
?>
        <table>
        <tr><th>Name</th> <th>Join date</th> <th>Email</th> <th>Website</th> <th>Edit</th></tr>
<?php   foreach ($members as $member) { ?>
            <tr>
            <td><?php echo $member['name']; ?></td>
            <td><?php echo date_format(date_create($member['date']), "Y-m-d"); ?></td>
            <td><?php echo $member['email']; ?></td>
            <td><a href="<?php echo $member['url']; ?>"><?php echo $member['title']; ?></a></td>
            <td><a href="?p=edit&<?php echo $status; ?>=<?php echo $member['id']; ?>">View/Edit</a></td>
            </tr>
<?php   } ?>
        </table>
<?php }

    function addUpdate($details) {
        $file = fopen("updates.csv","a");
        $update = array(date("Y-m-d H:i:s"), $details);
        fputcsv($file, $update);
        fclose($file);
    }
    
    function deleteMember($id, $filepath, $headers) {
        try {
            $members = toArray($filepath);
            $key = array_search($id, array_column($members, 'id'));
            if ($key !== false) {
                unset($members[$key]);
                
                $file = fopen($filepath,"w");
                fputcsv($file, $headers);
                foreach ($members as $member) {
                     fputcsv($file, $member);
                }
                fclose($file);
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }
    
    function updateMember ($id, $date, $name, $email, $title, $url, $mature, $country, $button, $tags, $comment, $headers) {
        try {
            $newMember = array($id, $date, $name, $email, $title, $url, $mature, $country, $button, $tags, $comment);
            $members = toArray("members.csv");
            $key = array_search($id, array_column($members, 'id'));
            if ($key !== false) {
                $members[$key] = $newMember;
            
                $file = fopen("members.csv","w");
                fputcsv($file, $headers);
                foreach ($members as $row) {
                  fputcsv($file, $row);
                }
                fclose($file);
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }
 ?>