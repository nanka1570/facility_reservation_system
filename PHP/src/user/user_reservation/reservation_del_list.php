<?php 
include_once "../../common/connect.php"; 
include_once "../../common/session.php"; 
//include_once "sanitize.php"; 
?> 
<!DOCTYPE html> 
<html> 
<head> 
<meta charset="UTF-8"> 
<link rel="stylesheet" href="../../common/user_basic.css">
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
        print '<p class="check">ユーザーIDが入力されていません。</p>'; 
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
            WHERE user_id = :user_Id AND start_time_of_use > :currentDateTime AND cancel_flag='' ";
    $stmt = $dbh->prepare($sql); 
    //var_dump($stmt);
    //echo '<br>';

    $stmt->bindParam(':user_Id', $user_Id); 
    $stmt->bindParam(':currentDateTime', $formattedCurrentDateTime, PDO::PARAM_STR);

    $stmt -> execute();
    //var_dump($stmt->execute()); 
     print '<p class="title">予約状況一覧</p><br>'; 
    if($stmt -> rowCount() == 0)
    {
        print '<p class="check">予約が見つかりませんでした。</p>';
        print '<form action="../../login/user_home.php">';
        print '<p class="buttons"><input type="submit" class="color" value="ホーム画面に戻る"></p>';
        print '</form>';
    }
    else
    {
        print '<form action="reservation_del_check.php" method="post">'; 
        //$reservation_number = 0;
        print'<table>';
            print'<tr>';
                print'<th scope="col" class="th_color"></th>';
                print'<th scope="col" class="th_color">部屋名</th>';
                print'<th scope="col" class="th_color">使用人数</th>';
                print'<th scope="col" class="th_color">利用開始日時</th>';
                print'<th scope="col" class="th_color">利用終了日時</th>';
                print'<th scope="col" class="th_color">合計料金</th>';
                print'<th scope="col" class="th_color">備考欄</th>';
            print'</tr>';
        while(true) 
        {  
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            if($rec == false)
            {
                break;
            }
            $reservation_number = $rec['reservation_number'];
            //print '<input type="radio" name="selected_reservation_number" value="'.$reservation_number.'">';
                /*"
                  data-room-number="'.$rec['room_number'].'"
                  data-number_of_user="'.$rec['number_of_user'].'">';*/
            

            //予約情報の表示
            //print '予約番号：'.$rec['reservation_number']." ";
            //print 'ユーザーID：'.$rec['user_id']." ";
            //print '部屋番号：'.$rec['room_number']." ";
            $room_number = $rec['room_number'];
            $sql2 = "SELECT room_name FROM facility_table WHERE room_number = ?";
            $stmt2 = $dbh->prepare($sql2);
            $stmt2->execute([$room_number]);
            $rec2 = $stmt2->fetch(PDO::FETCH_ASSOC);

            print'<tr>';
                print'<td class="list"><input type="radio" name="selected_reservation_number" value="'.$reservation_number.'"</td>';
                print'<td class="list">'.$rec2['room_name'].'</td>';
                print'<td class="list">'.$rec['number_of_user'].'</td>';
                print'<td class="list">'.$rec['start_time_of_use'].'</td>';
                print'<td class="list">'.$rec['end_time_of_use'].'</td>';
                print'<td class="list">'.$rec['sum_of_price'].'</td>';
                print'<td class="list">'.$rec['remark'].'</td>';
            print'</tr>';

            $reservation_number++;
        }
        print '</table>';           
    
            print '<p class="buttons"><input type="submit" class="color" value="選択した予約をキャンセル"></p>'; 
        print '</form>';
        $dateToday = date("Y-m-d");
        print'<p class="check">日付で検索</p>';
        print'<form method="post" action="reservation_del_list_search.php">';
            print'<p class="check">日付</p>';
            print'<p class="check"><input type="date" class="date" name="search_date" min="'.$dateToday.'"><br/>';
            print'<p class="buttons"><input type="submit" class="color" value="検索"></p>';
        print'</form>';
        print '<form action="../../login/user_home.php">';
            print '<p class="buttons"><input type="submit" class="color" value="ホーム画面に戻る"></p>';
        print '</form>';
         
    }
    $dbh = null;
} 
catch (Exception $e) 
{ 
    echo 'エラー: ' . $e->getMessage(); 
    exit(); 
} 
?> 

</body> 
<script src="../../common/ebi.js"></script>
</html>