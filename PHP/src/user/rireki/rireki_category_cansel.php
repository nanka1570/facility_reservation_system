<?php
//include_once "common/connect.php";
//include_once "common/session.php";
include_once "../../common/connect.php";
include_once "../../common/session.php";
$_SESSION['pagename']="利用履歴";
include_once "../../login/user_home.php";
$_SESSION['pagename']="ホームページ";
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="rireki.css">
<title>終了時間ソート</title>
<body>
<?php
print'<p class="title">キャンセル済み予約</p>';

print '<form method="post" action="rireki.php">';
print '<input type="submit" class="button2" name="back" value="戻る"><br>';
print '<input type="hidden" class="pagename" name="pagename" value="利用履歴"></form>';

print '<br/>';
print'<table class="histable">';
print"<tr>";
print'<th class="histh">部屋名</th>';
print'<th class="histh">利用人数</th>';
print'<th class="histh">開始時間</th>';
print'<th class="histh">終了時間</th>';
print'<th class="histh">合計金額</th>';
print'<th class="histh">備考</th>';
print"</tr>";
$id=$_SESSION['user_Id'];
try{
    $dbh=$conn; 
    
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql="SELECT room_name,number_of_user,start_time_of_use,end_time_of_use,sum_of_price,remark from history_table WHERE user_id='$id' and cancel_flag='C'";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();

    while(true)
    {
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==false){ 
            break;
        }
        $room_number=$rec['room_name'];
        $newDateTime=new Datetime($rec['start_time_of_use']);
        $start=$newDateTime->format("Y/m/d H:i");
        $newDateTime=new Datetime($rec['end_time_of_use']);
        $end=$newDateTime->format("Y/m/d H:i");
        print "<tr>";
        print'<td class="histd">';print $rec['room_name'];print "</td>";
        print'<td class="histd">';print $rec['number_of_user'];print "</td>";
        print'<td class="histd">';print $start; print "</td>";
        print'<td class="histd">';print $end;print "</td>";
        print'<td class="histd">';print $rec['sum_of_price'];print "</td>";
        print'<td class="histd">';print $rec['remark'];print "</td>"; 
        print "</tr>"; 
    }
}catch(Exception $e){
        print $e;
    print'error!';
    exit();
    }
    ?>
        </table>
            </body>
            </html>