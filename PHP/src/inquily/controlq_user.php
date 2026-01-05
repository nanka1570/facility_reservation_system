<?php
include_once "../common/connect.php";
include_once "../common/session.php";
$_SESSION['pagename']="問合せ";
include_once "../login/user_home.php";
$_SESSION['pagename']="ホームページ";
$result = "";
    if (isset($_POST['sousin'])) {
        $te=$_POST['hensin'];
    $id=$_SESSION['user_Id'];
    try{
    $user_id=$id;
    $text2=$te;
    $inquily_time=date("Y/m/d H:i:s");
    $text_category="Y";
    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql='INSERT INTO inquily_table("user_id",serial_number,"text",inquily_time,"text_category")
    VALUES(?,(SELECT COALESCE(MAX(serial_number)+1,0)FROM inquily_table),?,?,?)';
    $stmt=$dbh->prepare($sql);
    $data[]=$user_id;
    $data[]=$text2;
    $data[]=$inquily_time;
    $data[]=$text_category;
    $stmt->execute($data);
    $dbh=null;

    }
    catch(Exception $e){
    print $e;
    print 'ただいま障害により大変ご迷惑をお掛けしております。';
    exit();
    }
        $result = "送信しました";
    }
    elseif (isset($_POST['update'])) {
        $result = "更新しました";
    }
    elseif(isset($_POST['delete'])) {
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
            //print $e;
            //print'ないよ';
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
        //print $e;
        //print'ないよ';
        exit();
    }
    }
        // $result = "削除しました";
    }
    //echo $result;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF=8">
<link rel="stylesheet" href="textclass.css">
<title>???</title>
</head>
<body>
    
<?php

    try{
    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
   
    $ID=$_SESSION['user_Id'];

    print '<br>';
    $sql="SELECT * FROM inquily_table WHERE user_id = '$ID' ";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
?>
    <table>
    <?php
         while(true){
            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            if($rec==false){
                break;
            }else{
                $newDateTime=new Datetime($rec['inquily_time']);
                $time=$newDateTime->format("Y/m/d H:i");
            if($rec['text_category']=="A"){
                $textclass="A";
                print "<tr><td class='left'><p class=left_bg>".$time."<br>";
                print $rec['text']."</p></td>";
                print "<td class=right></td></tr>";
            }else{
                $textclass="U";
                $serial=$rec['serial_number'];
                print "<tr><td class='right'></td>";
                print '<form action="controlq_user.php" method="post">';
                print "<td class=right><p class=right_bg>".$time;
                print '<input class="delete" type="submit" onclick="return confirm_test()" name="delete" value="削除">';

                print '<input type="hidden" name="serial" value="'.$serial.'">';
                print '</form><br>';
                print $rec['text']."</p></td></tr>";
            }
            }
        }
    }

catch(Exception $e){
    print $e;
    print'error!';
    exit();
}  
?>
</table>
<br><br><br><br><br><br><br id="bottom">
<form action="controlq_user.php" method="post">

<textarea rows="10" name="hensin" font-size="40px"></textarea>

<input class="sousin" type="submit" onclick="return confirm_test()" name="sousin" value ="送信">
</form>
</p>
<script>
let target = document.getElementById('bottom');
target.scrollIntoView(false);

function confirm_test(){
    var select = confirm("実行しますか？");
    return select;
    function okfunc(){
        document.contactform.submit();
    }

}
</script>
</body>
</html>