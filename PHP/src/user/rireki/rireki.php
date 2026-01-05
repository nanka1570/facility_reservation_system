<?php 
include_once "../../common/connect.php";
include_once "../../common/session.php";
$_SESSION['pagename']="利用履歴";
include_once "../../login/user_home.php";
$_SESSION['pagename']="ホームページ";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="rireki.css">
<title>履歴</title>
</head>
<body class="hisbody">

<?php
print '<p class="font">絞り込み　</p>';

print '<form method="post" action="rireki_category_room_number.php">';
print '<input type="submit" class="button2" name="back" value="部屋名">';
print '</form>';

print '<form method="post" action="rireki_category_date.php">';
print '<input type="submit" class="button2" name="back" value="利用日時">';
print '</form>';

print '<form method="post" action="rireki_category_cansel.php">';
print '<input type="submit" class="button2" name="back" value="キャンセル済み">';
print '</form>';

print '<form method="post" action="rireki.php">';
print '<input type="submit" class="button2" name="reload" value="絞り込みクリア">';
print '</form>';

print'<table class="histable" align=left width=100% >';
print'<tr>';
print'<th class="histh">部屋名</th>';
print'<th class="histh">利用人数</th>';
print'<th class="histh">開始時間</th>';
print'<th class="histh">終了時間</th>';
print'<th class="histh">合計金額</th>';
print'<th class="histh">備考</th>';
print"</tr>";
print '<br/>';
?>

<?php
if(isset($_POST['cate']))
{
$cate=$_POST['cate'];
}else{
    $cate="user_id";
}
if(isset($_POST['atai']))
{
    $atai=$_POST['atai'];
}else{
    $atai=$_SESSION['user_Id'];
}

if($cate=='start_time_of_use'){
    try{

       $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $id=$_SESSION['user_Id'];
    $sql="SELECT room_name,number_of_user,start_time_of_use,end_time_of_use,sum_of_price,remark FROM history_table
     WHERE user_id ='$id' ORDER BY start_time_of_use ASC";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    while(true)
    {
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==false){ 
            break;
        }
        $time=(string)$rec['start_time_of_use'];
        if(strpos($time,$atai)===false){
            continue;
        }
       $start=(string)$rec['start_time_of_use'];
       $start=mb_substr($start,0,16);
       $end=(string)$rec['end_time_of_use'];
       $end=mb_substr($end,0,16);
        
        print '<tr>';
        print '<td class="histd">';print $rec['room_name'];print "</td>";
        print '<td class="histd">';print $rec['number_of_user'];print "</td>";
        print '<td class="histd">';print $start; print "</td>"; 
        print '<td class="histd">';print $end;print "</td>"; 
        print '<td class="histd">';print $rec['sum_of_price'];print "</td>";
        print '<td class="histd">';print $rec['remark'];print "</td>";  
        print "</tr>";   
    }
    

    }catch(Exception $e){
        print $e;
        print 'error!';
    }
}
else{//部屋名で検索
    try{
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $id=$_SESSION['user_Id'];
        $sql="SELECT room_name,number_of_user,start_time_of_use,end_time_of_use,sum_of_price,remark FROM history_table WHERE user_id ='$id' and $cate = '$atai' ORDER BY start_time_of_use ASC";
        $stmt=$dbh->prepare($sql);
        $stmt->execute();
    while(true)
    {
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==false){ 
            break;
        }
        
        $start=(string)$rec['start_time_of_use'];
        $start=mb_substr($start,0,16);
        $end=(string)$rec['end_time_of_use'];
        $end=mb_substr($end,0,16);
        print "<tr>";
        print '<td class="histd">';print $rec['room_name'];print "</td>";
        print '<td class="histd">';print $rec['number_of_user'];print "</td>";
        print '<td class="histd">';print $start; print "</td>"; 
        print '<td class="histd">';print $end;print "</td>"; 
        print '<td class="histd">';print $rec['sum_of_price'];print "</td>";
        print '<td class="histd">';print $rec['remark'];print "</td>";  
        print "</tr>";
    }
}catch(Exception $e){
    print $e;
    print 'error!';
}
print'<br />'; 
}
?>
</table>
</body>
</html>