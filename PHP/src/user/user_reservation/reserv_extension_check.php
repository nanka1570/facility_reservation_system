<?php
include_once "../../common/connect.php"; 
include_once "../../common/session.php"; 
//var_dump($_POST);
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
        // case $_POST == null:
        //     print '<br><br><br><br>';
        //     print'<p class="check">対象の予約を選択してください</p>';
        //     print'<p class="check">一覧画面に戻って選択し直してください</p>';
        //     print'<form action="reserv_extension_list.php">';
        //     print'<p class="buttons"><input type="submit" class="button" value="予約一覧画面に戻る"></p>';
        //     print'</form>';
        //     break;

        // case $_POST['selected_extension_minute'] == null:
        //     print '<br><br><br><br>';
        //     print'<p class="check">予約時間が選択されていません</p>';
        //     print'<p class="check">一覧画面に戻って選択し直してください</p>';
        //     print'<form action="reserv_extension_list.php">';
        //     print'<p class="buttons"><input type="submit" class="button" value="予約一覧画面に戻る"></p>';
        //     print'</form>';
        //     break;

        // case $_POST['selected_reservation_number'] == null:
        //     print '<br><br><br><br>';
        //     print'<p class="check">対象の予約を選択してください</p>';
        //     print'<p class="check">一覧画面に戻って選択し直してください</p>';
        //     print'<form action="reserv_extension_list.php">';
        //     print'<p class="buttons"><input type="submit" class="button" value="予約一覧画面に戻る"></p>';
        //     print'</form>';
        //     break;

        if(!isset($_POST['selected_extension_minute'])){
            include_once "reserv_extension_list.php";
            print '<br><br><br><br>';
            print'<p class="check">予約時間が選択されていません</p>';
            exit();
        }

        if(!isset($_POST['selected_reservation_number'])){
            include_once "reserv_extension_list.php";
            print '<br><br><br><br>';
            print'<p class="check">対象の予約が選択されていません</p>';
            exit();
        }
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

            // $room_name = $rec['room_name'];
            // $sql3 = "SELECT category_number from facility_table WHERE room_name = ?";//学校用
            // $stmt3 = $conn->prepare($sql3);
            // $stmt3->execute([$room_name]);
            // $data = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            $extention_minute = $_POST['selected_extension_minute'];
            

            $room_number = (int)$room_number;

            //選択された部屋の時間延長が可能かどうかを調べる
            $sql_time = 'SELECT * FROM facility_table WHERE room_number = :room_number';
            $stmt = $conn -> prepare($sql_time);
            $stmt->bindParam(':room_number', $room_number);

            $stmt->execute();
            $rec2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if($rec2['time_extension'] === '可'){
                    print'<p class="check"><こちらの予約を'.$extention_minute.'分延長しますか？></p>';
                    print'<p class="check">予約番号：'.$reservation_number.'</p>';
                    print'<p class="check">ユーザーID：'.$reservation_data['user_id'].'</p>';//学校用
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

                    print '<form action="reserv_extension_done.php" method="POST">';
                    print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                    print'<input type="hidden" name="user_id" value="'.$reservation_data['user_id'].'">';
                    print'<input type="hidden" name="room_number" value="'.$room_number.'">';
                    print'<input type="hidden" name="start_time_of_use" value="'.$reservation_data['start_time_of_use'].'">';
                    print'<input type="hidden" name="end_time_of_use" value="'.$reservation_data['end_time_of_use'].'">';
                    print'<input type="hidden" name="selected_extension_minute" value="'.$extention_minute.'">';
                    print '<p class="buttons"><input type="submit" class="button" value="はい"></p>';
                    print '</form>';
                    //include_once "reserv_extension_done.php";
                }else{
                    print'<p class="check">予約時間の延長はできません。</p><br> <p class="check">管理者に問い合わせてください。</p>';
                    print'<form action="reserv_extension_list.php">';
                    print'<p class="buttons"><input type="submit" class="button" value="予約一覧画面に戻る"></p>';
                    print'</form>';
                }
            
            //編集したい予約の日付データだけを引き継ぐ       
            $reserveDate = $reservation_data['start_time_of_use'];
            $dateToday = date("Y-m-d");
            $newDateTime=new Datetime($reserveDate);//
            $edit_reservation_date=$newDateTime->format("Y-m-d");//

            // if($dateToday == $edit_reservation_date){
            // print'<form method="post" action="reservation_edit_01.php">';
            //     print'<input type="hidden" name="room_ctg" value="'.$data['category_number'].'">';
            //     print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
            //     //print'<input type="hidden" name="user_id" value="'.$reservation_data['user_Id'].'">';
            //     print'<input type="hidden" name="user_id" value="'.$reservation_data['user_id'].'">';//学校用
            //     //print'<input type="hidden" name="start_time_of_use" value="'.$reservation_data['start_time_of_use'].'">';
            //     print'<p class="buttons"><input type="submit" class="button" value="延長する"></p>';
            // print'</form>';
            // }
            // else{
            // print'<form method="post" action="reservation_edit_02.php">';
            //     print'<input type="hidden" name="room_ctg" value="'.$data['category_number'].'">';
            //     print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
            //     //print'<input type="hidden" name="user_id" value="'.$reservation_data['user_Id'].'">';
            //     print'<input type="hidden" name="date" value="'.$edit_reservation_date.'">';
            //     print'<input type="hidden" name="user_id" value="'.$reservation_data['user_id'].'">';//学校用
            //     //print'<input type="hidden" name="start_time_of_use" value="'.$reservation_data['start_time_of_use'].'">';
            //     print'<p class="buttons"><input type="submit" class="button" value="変更する"></p>';
            // print'</form>';            
            // 
?>
    <p class="buttons"><button onclick="location.href='reserv_extension_list.php'" class="button">予約時間の延長を中止する</button></p>
</body>
</html>