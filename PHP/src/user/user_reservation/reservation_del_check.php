<?php
include_once "../../common/connect.php"; 
include_once "../../common/session.php"; 

//var_dump($_POST);
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
    if($_POST == null){
        print '<br><br><br><br>';
        print'<p class="check">予約が選択されていません</p>';
        print'<form action="reservation_del_list.php">';
        print'<p class="buttons"><input type="submit" class="color" value="予約一覧画面に戻る"></p>';
        print'</form>';
    }else{
        $reservation_number = $_POST['selected_reservation_number'];
        $sql = "SELECT * FROM reservation_table WHERE reservation_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reservation_number]);
        $reservation_data = $stmt->fetch();
        //$reservation_data = $stmt;
        //var_dump($reservation_data);

        $room_number = $reservation_data['room_number'];
        $sql2 = "SELECT room_name FROM facility_table WHERE room_number = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$room_number]);
        $rec = $stmt2->fetch(PDO::FETCH_ASSOC);

        print'<p class="check"><こちらの予約をキャンセルしますか？></p>';
        print'<p class="check">部屋名：'.$rec['room_name'].'</p>';
        print'<p class="check">利用人数：'.$reservation_data['number_of_user'].'人</p>';
        print'<p class="check">利用開始日時：'.$reservation_data['start_time_of_use'].'</p>';
        print'<p class="check">利用終了日時：'.$reservation_data['end_time_of_use'].'</p>';
        print'<p class="check">合計料金：'.$reservation_data['sum_of_price'].'円</p>';
        if($reservation_data['remark']==null){
            print'<p class="check">備考欄：なし</p>';
        }
        else{
            print'<p class="check">備考欄：'.$reservation_data['remark'].'</p>';
        }
        print'<form action="reservation_del_list.php">';
            print'<p class="buttons"><input type="submit" class="color" value="予約一覧画面に戻る"></p>';
        print'</form>';
        print'<form method="post" action="reservation_del_done.php">';
            print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
            print'<p class="buttons"><input type="submit" class="color" value="予約をキャンセルする"></p>';
        print'</form>';
    }
    ?>
</body>
</html>