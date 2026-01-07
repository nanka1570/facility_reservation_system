<?php 
include_once "../../common/DB_switch.php";
// include_once "../../common/connect.php"; 
include_once "../../common/session.php"; 
?> 
<!DOCTYPE html> 
<html> 
<head> 
<meta charset="UTF-8"> 
<link rel="stylesheet" href="../../common/admin_basic.css">
<link rel="stylesheet" href="del_list.css"> 
<title>予約状況一覧</title> 
</head> 
<body>
    <header class="admin-header">
        <h1>予約状況一覧</h1>
    </header>
    
<?php
try 
{ 
    $user_Id = $_SESSION['user_Id']; 
    if ($user_Id == "") 
    { 
        print '管理者IDが入力されていません。<br/>'; 
        include_once "../../login/user_login.php";  
        exit(); 
    }

    date_default_timezone_set('Asia/Tokyo'); 
    $dbh = $conn; 
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $currentDateTime = new DateTime();
    $formattedCurrentDateTime = $currentDateTime->format('Y-m-d H:i:s'); 
    $dateToday = date("Y-m-d");

    print '<div class="search-section">';
        print '<h2>予約検索</h2>';
        print '<form method="post" action="admin_reservation_del_list_search.php" class="search-form">';
            print '<div class="form-group">';
                print '<label for="search_user_name">ユーザーID</label>';
                print '<input id="search_user_name" name="search_user_name" type="text" placeholder="例: user1">';
            print '</div>';
            print '<div class="form-group">';
                print '<label for="search_date">日付</label>';
                print '<input id="search_date" name="search_date" type="date" min="'.$dateToday.'">';
            print '</div>';
            print '<button type="submit" class="search-button">検索</button>';
        print '</form>';
    print '</div>';

    $sql = "SELECT * 
            FROM reservation_table 
            WHERE start_time_of_use > :currentDateTime AND cancel_flag='' ";
    $stmt = $dbh->prepare($sql); 
    $stmt->bindParam(':currentDateTime', $formattedCurrentDateTime, PDO::PARAM_STR);
    $stmt -> execute();

    if($stmt -> rowCount() == 0)
    {
        print'<p class="no-results">予約が見つかりませんでした。</p>';
        print '<input type="button" class="home-button" onclick=location.href="../../login/admin_home.php" name="back" value="ホームへ戻る">';
    }
    else
    {
        print '<form action="admin_reservation_del_check.php" method="post" class="reservation-form">'; 
        print '<div class="table-container">';
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
                    print'<td><input type="radio" name="selected_reservation_number" value="'.$reservation_number.'"</td>';
                    print'<td>'.$rec['user_id'].'</td>';
                    print'<td>'.$rec2['room_name'].'</td>';
                    print'<td>'.$rec['number_of_user'].'</td>';
                    print'<td>'.$rec['start_time_of_use'].'</td>';
                    print'<td>'.$rec['end_time_of_use'].'</td>';
                    print'<td>'.$rec['sum_of_price'].'</td>';
                    print'<td>'.$rec['remark'].'</td>';
                print'</tr>';
            }
            print '</table>';
        print '</div>';
        
        print '<div class="button-container">';
            print '<input type="submit" value="選択した予約をキャンセル" class="cancel-button">'; 
        print '</div>';
        print '</form>';

        print '<div class="button-container">';
            print '<form>';
                print '<input type="button" onclick=location.href="../../login/admin_home.php" value="ホーム画面に戻る" class="home-button">';
            print '</form>';
        print '</div>';
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

<script src="../../common/ebi.js"></script>

</html>