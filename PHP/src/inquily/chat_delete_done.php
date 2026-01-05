<?php
include_once "../common/connect.php";
include_once "../common/session.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF=8">
<title>aaa</title>
<body>
    <?php
    $_POST['user_id']="user1";
    if(isset($_POST['user_id']))
    {
    $ID=$_POST['user_id'];
    }
    print'<input type="hidden" name="user_id" value="$ID">';
    $serial_num=$_POST['serial'];
    $id=$_SESSION['user_Id'];
    if($id=="0"){
        try{
        
            $dbh=$conn;
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $sql="DELETE FROM inquily_table WHERE user_id ='$ID' and serial_number='$serial_num'";
            $stmt=$conn->prepare($sql);
            $stmt->execute();
            
         }
        catch(Exception $e){
            print $e;
            print'ないよ';
            exit();
        }
    }else{
    try{
        
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql="DELETE FROM inquily_table WHERE user_id ='$id' and serial_number='$serial_num'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
       
     }
    catch(Exception $e){
        print $e;
        print'ないよ';
        exit();
    }
    }?>
<?php
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
?>