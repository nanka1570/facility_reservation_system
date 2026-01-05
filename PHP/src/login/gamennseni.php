<?php
include_once "../common/connect.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>部屋選択</title>
        <body>
<?php
try{
    $dbh=$conn; 
    
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    ?>

    <?php
    print '<br>';
    $sql="SELECT room_number,room_name FROM facility_table  ";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    
    

while(true)
        {
            $text=$stmt->fetch(PDO::FETCH_ASSOC);
            if($text==false)
            {
                break;
            }
            $room_name=$text["room_name"];
            $room_number=$text["room_number"];
            print'<br />';
            print'<form method=POST action="gamenn.php">';
            print'<input type=hidden  name="room_name" value="'.$room_name.'">';
            print'<form method=POST action="gamenn.php">';
            print'<input type=hidden  name="room_number" value="'.$room_number.'">';
             //$_SESSION['user']=$user_id;   
            
            print'<input type="submit" name="selectQ" onclick=""value="'.$text['room_name'].'">';
            print '</form>';
        }
    }
    catch(Exception $e){
        print $e;
    print'error!';
    exit();
        }
         ?>
            </body>
            </html>
