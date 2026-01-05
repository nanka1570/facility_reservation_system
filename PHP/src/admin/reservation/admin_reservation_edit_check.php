<?php
include_once "../../common/DB_switch.php";
// include_once "../../common/connect.php"; 
include_once "../../common/session.php";  
//var_dump($_POST);
?>
<!DOCTYPE html> 
<html> 
<head> 
<meta charset="UTF-8"> 
<link rel="stylesheet" href="../../common/admin_basic.css">
<link rel="stylesheet" href="check.css">  
<title>予約の変更</title> 
</head> 
<body> 
    <header class="admin-header">
        <h1>予約の変更</h1>
    </header>

    <?php
        $reservation_number = $_POST['selected_reservation_number'];
        $sql = "SELECT * FROM reservation_table WHERE reservation_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reservation_number]);
        $reservation_data = $stmt->fetch();
        //var_dump($reservation_data);

        $room_number = $reservation_data['room_number'];
        $sql2 = "SELECT room_name FROM facility_table WHERE room_number = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$room_number]);
        $rec = $stmt2->fetch(PDO::FETCH_ASSOC);

        //仮で予約をキャンセルする

    //<div class="content">を追加
    print '<div class="content">';

        print'<p><こちらの予約を変更しますか？></p>';
        //print'<p>ユーザーID：'.$reservation_data['user_Id'].'</p>';
        print'<p>ユーザーID：'.$reservation_data['user_id'].'</p>';//学校用
        print'<p>部屋名：'.$rec['room_name'].'</p>';
        print'<p>利用人数：'.$reservation_data['number_of_user'].'人</p>';
        print'<p>利用開始日時：'.$reservation_data['start_time_of_use'].'</p>';
        print'<p>利用終了日時：'.$reservation_data['end_time_of_use'].'</p>';
        print'<p>合計料金：'.$reservation_data['sum_of_price'].'円</p>';
        if($reservation_data['remark']==null){
            print'<p>備考欄：なし</p>';
        }
        else{
            print'<p>備考欄：'.$reservation_data['remark'].'</p>';
        }

    //</div>を追加
    print '</div>';

        $room_name = $rec['room_name'];
        $sql3 = "SELECT category_number from facility_table WHERE room_name = ?";//学校用
        $stmt3 = $conn->prepare($sql3);
        $stmt3->execute([$room_name]);
        $data = $stmt3->fetch(PDO::FETCH_ASSOC);
        //var_dump($data);

        //仮で予約をキャンセル
        //$reservation_number = $_POST['reservation_number'];
        //$user_id = $reservation_data['user_Id'];
        $user_id = $reservation_data['user_id'];//学校用
        $sql4 = "UPDATE reservation_table SET cancel_flag = 'C' WHERE reservation_number = ?";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->execute([$reservation_number]);

        //仮で予約履歴をキャンセル
        $user_id = $reservation_data['user_id'];//学校用
        $sql4 = "UPDATE history_table SET cancel_flag = 'C' WHERE reservation_number = ?";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->execute([$reservation_number]);
        
        //編集したい予約の日付データだけを引き継ぐ       
        $reserveDate = $reservation_data['start_time_of_use'];
        $dateToday = date("Y-m-d");
        $newDateTime=new Datetime($reserveDate);//
        $edit_reservation_date=$newDateTime->format("Y-m-d");//

        if($dateToday == $edit_reservation_date){
        print'<form method="post" action="admin_reservation_edit_01.php">';
            print'<input type="hidden" name="room_ctg" value="'.$data['category_number'].'">';
            print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
            //print'<input type="hidden" name="user_id" value="'.$reservation_data['user_Id'].'">';
            print'<input type="hidden" name="user_id" value="'.$reservation_data['user_id'].'">';//学校用
            //print'<input type="hidden" name="start_time_of_use" value="'.$reservation_data['start_time_of_use'].'">';
            print'<input type="submit" value="変更する">';
        print'</form>';
        }
        else{
        print'<form method="post" action="admin_reservation_edit_02.php">';
            print'<input type="hidden" name="room_ctg" value="'.$data['category_number'].'">';
            print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
            //print'<input type="hidden" name="user_id" value="'.$reservation_data['user_Id'].'">';
            print'<input type="hidden" name="date" value="'.$edit_reservation_date.'">';
            print'<input type="hidden" name="user_id" value="'.$reservation_data['user_id'].'">';//学校用
            //print'<input type="hidden" name="start_time_of_use" value="'.$reservation_data['start_time_of_use'].'">';
            print'<input type="submit" value="変更する" class="button">';
        print'</form>';            
        }

        print'<form method="post" action="admin_reservation_edit_stop.php">';//
            print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';//
            print'<input type="submit" value="予約変更を中止する" class="button">';//
        print'</form>';
    ?>
</body>
</html>