<?php
include_once "../common/connect.php";
include_once "../common/session.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF=8">
<link rel="stylesheet" href="rireki.css">
<title>aaa</title>
<body>
    <?php
    $_POST['user_id']="user1";
    if(isset($_POST['user_id']))
    {
    $ID=$_POST['user_id'];
    }
    $id=$_SESSION['user_Id']; 
    print"<table align=center width=100% >";
    print"<tr>";
    print"<th></th>";
    print"<th>内容</th>";
    print"<th>入力時間</th>";
    print"</tr>";
   if($id=="0"){  
   try{
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  
      $sql="SELECT * FROM inquily_table
     WHERE user_id ='$ID' ";
     $stmt=$dbh->prepare($sql);
     $stmt->execute();
   
     while(true){
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==false)
        {
            break;
        }
        $serial=$rec['serial_number'];
       print"<tr>";
       print "<td>"; print '<form action="chat_delete_done.php" method="post">';
        print '<input type="submit" onclick="location.href="chat_delete_done.php"" name="'.$serial.'" value="削除">';
        print '<input type="hidden" name="serial" value="'.$serial.'">';
        print '<input type="hidden" name="user_id" value="'.$ID.'">';
        print '</form>';
        print "<td>";print $rec['text'];
        print "<td>";print $rec['inquily_time'];
        print "<td>";print $rec['text_category'];
        print "</tr>";
         }
     print "</table>";    
    }
    catch(Exception $e){
        print $e;
        print'ないよ';
        exit();
    }
    ?>
    <form action="controlq_kanri.php" method="post">
    <input type="submit" onclick="location.href=controlq_kanri.php" value="戻る">
    <?php
    print '<input type="hidden" name="user_id" value="'.$ID.'">';
    print'</form>';
    }
    else{
        try{   
            $dbh=$conn;
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);  
          $sql="SELECT * FROM inquily_table
         WHERE user_id ='$id' and text_category='Y' ";
         $stmt=$dbh->prepare($sql);
         $stmt->execute();
        
         while(true){
            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            if($rec==false)
            {
                break;
            }
            $serial=$rec['serial_number'];
           print"<tr>";
           print "<td>"; print '<form action="chat_delete_done.php" method="post">';
            print '<input type="submit" onclick="location.href="chat_delete_done.php"" name="'.$serial.'" value="削除">';
            print '<input type="hidden" name="serial" value="'.$serial.'">';
            print '</form>';
            print "<td>";print $rec['text'];
            print "<td>";print $rec['inquily_time'];
            print "<td>";print $rec['text_category'];
            print "</tr>";
             }
         print "</table>";    
            }
        catch(Exception $e){
            print $e;
            print'ないよ';
            exit();
        }
        if($id=="0"){
            print '<form action="controlq_kanri.php" method="post">';
            print '<input type="submit" name="back" value="戻る">';
            print'<input type="hidden" name="user_id" value="'.$ID.'">';
            print'</form>';
            }else{
                print '<form action="controlq_user.php" method="post">';
                print '<input type="submit" name="back" value="戻る">';
                print'</form>';
            }
      } ?>
</body>
</html>
