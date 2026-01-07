<?php
include_once "../../common/connect.php";
?>
<!DOCTYPE html>
<link rel="stylesheet" href="gamen.css">
<html>
<head>
<title>部屋選択</title>
<body>
<!-- 部屋を一つ選択して表示 -->
<?php
    try{
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
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
            $room_number=$text["room_number"];
            $room_name=$text["room_name"];
            
            print'<form method="post" action="gamenn.php" class=room>';            
            print'<input type=hidden  name="room_number" value="'.$room_number.'">';
            print'<input type=hidden  name="room_name" value="'.$room_name.'">';
            //$_SESSION['user']=$user_id;   
            print'<input type="submit" name="selectQ" onclick=""value="'.$text['room_name'].'"class=room_button ></form>';
            
        }
    }
    catch(Exception $e){
    print $e;
    print'error!';
    exit();
    }
    print '<p><input type="button" class="backbutton" onclick=location.href="../../login/admin_home.php" value="戻る">'; 
    print '　　　　　<input type="button" class="backbutton" onclick=location.href="gamennitiran.php" value="一覧表示へ"></p>';
?>
<!-- 部屋を複数選択して表示 -->
</body>
</html>