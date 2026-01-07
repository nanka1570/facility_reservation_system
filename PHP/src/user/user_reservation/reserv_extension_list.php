<?php 
include_once "../../common/connect.php"; 
include_once "../../common/session.php"; 
?> 
<!DOCTYPE html> 
<html> 
<head> 
<meta charset="UTF-8"> 
<link rel="stylesheet" href="reservation.css"> 
<title></title> 
</head> 
<body>
<?php
try 
{ 
    //$post = sanitize($_POST); 
    $user_Id = $_SESSION['user_Id'];
    if ($user_Id == "") 
    { 
        print '<p class="check">管理者IDが入力されていません。</p>'; 
        include_once "../../login/user_login.php"; 
        exit(); 
    }
    date_default_timezone_set('Asia/Tokyo'); 
    $dbh = $conn; 
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $currentDateTime = new DateTime();
    //時刻の取得 
    $formattedCurrentDateTime = $currentDateTime->format('Y-m-d H:i:s'); 
    // DateTimeオブジェクトを文字列に変換 
    $sql = "SELECT * 
            FROM reservation_table 
            WHERE start_time_of_use < :currentDateTime AND end_time_of_use >= :currentDateTime2 AND user_id = :user_Id AND cancel_flag='' ";//
    /*$sql = "SELECT * 
        FROM reservation_table 
        WHERE start_time_of_use > :currentDateTime AND user_id = :user_Id AND cancel_flag='' ";*/ //学校用
    $stmt = $dbh->prepare($sql); 
    $stmt->bindParam(':user_Id', $user_Id); 
    $stmt->bindParam(':currentDateTime', $formattedCurrentDateTime, PDO::PARAM_STR);
    $stmt->bindParam(':currentDateTime2', $formattedCurrentDateTime, PDO::PARAM_STR);
    $stmt -> execute();
    print'<p class="title">予約状況一覧</p>'; 

    if($stmt -> rowCount() == 0)
    {
        print '<p class="check">予約が見つかりませんでした。</p>';
        print '<form>';
        print '<input type="button" class="button" onclick=location.href="../../login/user_home.php" value="ホーム画面に戻る">';//
    print '</form>';
    }
    else
    {
        print '<form action="reserv_extension_check.php" method="post">'; 
        print'<table>';
            print'<tr>';
                print'<th scope="col"></th>';
                print'<th scope="col">ユーザーID</th>';
                print'<th scope="col">部屋名</th>';
                print'<th scope="col">使用人数</th>';
                print'<th scope="col">利用開始日時</th>';
                print'<th scope="col">利用終了日時</th>';
                print'<th scope="col">合計料金</th>';
                print'<th scope="col">備考欄</th>';
            print'</tr>';
        while(true) 
        {  
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            if($rec == false)
            {
                break;
            }
            $reservation_number = $rec['reservation_number'];

            $room_number = $rec['room_number'];
            $sql2 = "SELECT room_name FROM facility_table WHERE room_number = ?";
            $stmt2 = $dbh->prepare($sql2);
            $stmt2->execute([$room_number]);
            $rec2 = $stmt2->fetch(PDO::FETCH_ASSOC);

            print'<tr>';
                print'<td class="list"><input type="radio" name="selected_reservation_number" value="'.$reservation_number.'"</td>';
                //print'<td>'.$rec['user_Id'].'</td>';
                print'<td class="list">'.$rec['user_id'].'</td>';//学校用
                print'<td class="list">'.$rec2['room_name'].'</td>';
                print'<td class="list">'.$rec['number_of_user'].'</td>';
                print'<td class="list">'.$rec['start_time_of_use'].'</td>';
                print'<td class="list">'.$rec['end_time_of_use'].'</td>';
                print'<td class="list">'.$rec['sum_of_price'].'</td>';
                print'<td class="list">'.$rec['remark'].'</td>';
            print'</tr>';
        }

        //延長する時間
        print '<input type="radio" name="selected_extension_minute" value="15">15分<br>';
        print '<input type="radio" name="selected_extension_minute" value="30">30分<br>';
        print '<input type="radio" name="selected_extension_minute" value="45">45分<br>';
        print '<input type="radio" name="selected_extension_minute" value="60">60分<br>';

        print '</table>';
        print '<br>';
            print '<p class="buttons"><input type="submit" class="button" value="選択した予約の終了時間を延長する"></p>'; 
        print '</form>';

        $dateToday = date("Y-m-d");
        // print'<p class="check">検索</p>';
        // print'<form method="post" action="reservation_edit_list_search.php">';
        //     print'<p class="check">日付</p>';
        //     print'<p class="check"><input type="date" class="date" name="search_date" min="'.$dateToday.'"></p>';
        //     print'<p class="buttons"><input type="submit" class="button" value="検索"></p>';
        // print'</form>';

        print '<form>';
            print '<p class="buttons"><input type="button" class="button" onclick=location.href="../../login/user_home.php" value="ホーム画面に戻る"></p>';//
        print '</form>';
         
    }
    $dbh = null;
    $stmt = null;
    $stmt2 = null;
} 
catch (Exception $e) 
{ 
    echo 'エラー: ' . $e->getMessage(); 
    exit(); 
} 
?>
</body>
</html>