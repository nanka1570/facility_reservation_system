<?php
//include_once "../connect.php";
//include_once "../session.php";
include_once "../common/connect.php";
include_once "../common/session.php";

$result = "";
if (isset($_POST['sousin'])) {
    $te=$_POST['hensin'];
    $id=$_POST['ID'];
    try{
        $user_id=$id;
        $text2=$te;
        $inquily_time=date("Y/m/d H:i:s");
        $text_category="A";
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
        // print 'お問合せ内容を送信しました';
        // print '<form method="post" action="controlq_kanri.php">'; 
        print '<input type="hidden" name="user_id" value="'.$id.'">';
        // print '<input type="submit" onclick="location.href=controlq_kanri.php" value="戻る">';
        // print '</form>';
        // print '<br />';
        //print '<input type="submit" onclick=location.href="user_home.php" name="ba" value="ホームへ戻る">';
        // print '<input type="submit" onclick=location.href="../login/admin_home.php" name="ba" value="ホームへ戻る">';
        
    }
    catch(Exception $e){
        print $e;
        print 'ただいま障害により大変ご迷惑をお掛けしております。';
        exit();
    }
}
elseif(isset($_POST['delete'])) {
    if(isset($_POST['ID']))
    {
    $ID=$_POST['ID'];
    }
    print'<input type="hidden" name="user_id" value="$ID">';
    $serial=$_POST['serial'];
    $id=$_SESSION['user_Id'];
    if($id=="0"){
        try{
        
            $dbh=$conn;
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $sql="DELETE FROM inquily_table WHERE user_id ='$ID' and serial_number='$serial'";
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
        $sql="DELETE FROM inquily_table WHERE user_id ='$id' and serial_number='$serial'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
       
     }
    catch(Exception $e){
        //print $e;
        //print'ないよ';
        exit();
    }
    }
}
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
    print'<br /><br />';
    if (isset($_POST['sousin'])) {
      $ID=$_POST['ID'];
    }
    elseif(isset($_POST['delete'])) {
        $ID=$_POST['ID'];
    }else{
        $ID=$_POST['user_id'];
    }
    try{
    $dbh=$conn; 
    
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    ?>
    <form action="getquestion2.php" method="post" name="back">
    <button class="top">お問い合わせトップへ</button>
    </form>
     <p class = "toptext">  <?php print $ID; ?>からのお問い合わせ</p>
    <?php
    print '<br>';
    $sql="SELECT * FROM inquily_table WHERE user_id = '$ID'";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    
    print "<table>";
    while(true){
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            if($rec==false){
                break;
            }
            else{
                $newDateTime=new Datetime($rec['inquily_time']);
                $time=$newDateTime->format("Y/m/d H:i");
            }
        if($rec['text_category']=='A'){
            $textclass="A";
            $serial=$rec['serial_number'];
            print '<form action="controlq_kanri.php" method="post">';
            print "<tr><td class='left'><p class=left_bg>".$time."";
            print '<input type="hidden" name="ID" value="'.$ID.'">';
            print '<input class="delete" type="submit" onclick="return confirm_test()"" name="delete" value="削除"><br>';
            print '<input type="hidden" name="serial" value="'.$serial.'">';
            print $rec['text']."</p></td></form>";
            print "<td class=right></td></tr>";
            
        }else{
            $textclass="U";
           
            print "<tr><td class='right'></td>";
            print '<form action="controlq_user.php" method="post">';
            print "<td class=right><p class=right_bg>".$time;
           
            
            
            print '</form><br>';
            print $rec['text']."</p></td></tr>";
        
        
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
<form action="controlq_kanri.php" method="post">
<textarea rows="10" name="hensin" font-size="40px" placeholder="<?php print $ID;?>に返信" ></textarea>
<?php
print '<input type="hidden" name="ID" value="'.$ID.'">';
?>
<!-- <p class="sousin"><?php// print $ID ?>へ<br><br> -->
<input class="sousin" type="submit" onclick="return confirm_test()" name="sousin" value ="送信"></form> 
<form action="getquestion2.php" method="post" name="fo">
<!-- <input type="submit" onclick="location.href=getquestion2.php" name="a" value="戻る"></form> -->
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