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
<title></title> 
</head> 
<body>
    <?php
//<div class="content">を追加
print '<div class="content">';

        $search_user_name = $_POST['search_user_name'];
        $search_date = $_POST['search_date'];
        $reservation_count = 0;
        $new_answer_date = 1;
        //var_dump($_POST);
        $dbh = $conn; 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $currentDateTime = new DateTime();
        $formattedCurrentDateTime = $currentDateTime->format('Y-m-d H:i:s');

        //ユーザーID、日時両方で検索
        if($search_user_name!= null && $search_date != null ){
            
            //$reservation_data[] = null;
            $sql = "SELECT * 
            FROM reservation_table 
            WHERE start_time_of_use > :currentDateTime  AND user_Id= :search_user_name AND cancel_flag='' ";//
            //WHERE start_time_of_use > :currentDateTime  AND user_Id= :search_user_name AND DATE_FORMAT(start_time_of_use, '%Y-%m-%d %H:%i:%s')  LIKE '%':search_date'%' AND cancel_flag='' ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':currentDateTime', $formattedCurrentDateTime, PDO::PARAM_STR);
            $stmt->bindParam(':search_user_name', $search_user_name, PDO::PARAM_STR);
            $stmt -> execute();

            while(true){
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);               
                if($rec==false){
                    break;
                }               
                //var_dump($reservation_data);
                $chack_date = (string)$rec['start_time_of_use'];
                //print $chack_date.'<br/>';
                $answer_date = strpos($chack_date,$search_date);
                //var_dump($answer_date);
                if($answer_date === false){
                    continue;
                    //$new_answer_date = 1;
                }
                if($answer_date === 0){
                    $new_answer_date = 0;
                }

            }
            $rec = null;
            //print $new_answer_date;

            if($new_answer_date==1){
                print'<p>予約がありません</p>';
            }    
            else{
            print '<form action="admin_reservation_edit_check.php" method="post">'; 
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
                $stmt -> execute();
                while(true){
                    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
                    //var_dump($rec);
                
                    if($rec==false){
                        break;
                    }
                    $chack_date = (string)$rec['start_time_of_use'];//
                    $answer_date = strpos($chack_date,$search_date);//


                    if($answer_date === 0){//
                    $reservation_number = $rec['reservation_number'];
                    $room_number = $rec['room_number'];
                    $sql2 = "SELECT room_name FROM facility_table WHERE room_number = ?";
                    //$sql2 = "SELECT room_name FROM category_table WHERE room_number = ?";
                    $stmt2 = $dbh->prepare($sql2);
                    $stmt2->execute([$room_number]);
                    $rec2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    print'<tr>';
                        print'<td><input type="radio" name="selected_reservation_number" value="'.$reservation_number.'"</td>';
                        //print'<td>'.$rec['user_Id'].'</td>';
                        print'<td>'.$rec['user_id'].'</td>';//学校用
                        print'<td>'.$rec2['room_name'].'</td>';
                        print'<td>'.$rec['number_of_user'].'</td>';
                        print'<td>'.$rec['start_time_of_use'].'</td>';
                        print'<td>'.$rec['end_time_of_use'].'</td>';
                        print'<td>'.$rec['sum_of_price'].'</td>';
                        print'<td>'.$rec['remark'].'</td>';
                    print'</tr>';
                    }//
                }
                $dbh = null;
                $stmt = null;
                $stmt2 = null;
                print'</table>';
                print'<input type="submit" value="選択した予約を変更する">';
            print'</form>';

            }
            
        }


        //ユーザーIDのみで検索する場合
        if($search_user_name != null && $search_date == null){
            $sql = "SELECT * 
            FROM reservation_table 
            WHERE start_time_of_use > :currentDateTime  AND user_Id= :search_user_name AND cancel_flag='' ";//

            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':currentDateTime', $formattedCurrentDateTime, PDO::PARAM_STR);
            $stmt->bindParam(':search_user_name', $search_user_name, PDO::PARAM_STR);
            $stmt -> execute();

            if($stmt -> rowCount() == 0)
            {
                print'<p>予約が見つかりませんでした。</p>';
                include_once "../../login/admin_home.php";
            }

            else
            {
                print '<form action="admin_reservation_edit_check.php" method="post">'; 
                //$reservation_number = 0;
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
                while(true){
                    $rec = $stmt->fetch(PDO::FETCH_ASSOC);              
                    if($rec==false){
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
                        //print'<td>'.$rec['user_Id'].'</td>';
                        print'<td>'.$rec['user_id'].'</td>';//学校用
                        print'<td>'.$rec2['room_name'].'</td>';
                        print'<td>'.$rec['number_of_user'].'</td>';
                        print'<td>'.$rec['start_time_of_use'].'</td>';
                        print'<td>'.$rec['end_time_of_use'].'</td>';
                        print'<td>'.$rec['sum_of_price'].'</td>';
                        print'<td>'.$rec['remark'].'</td>';
                    print'</tr>';
                }
                $dbh = null;
                $stmt = null;
                $stmt2 = null;
                print'</table>';
                print'<input type="submit" value="選択した予約を変更する">';
                print'</form>';
            }
        }
        

        //日付のみで検索する場合
        if($search_user_name == null && $search_date != null){
            $sql = "SELECT * 
            FROM reservation_table 
            WHERE start_time_of_use > :currentDateTime AND cancel_flag='' ";//
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':currentDateTime', $formattedCurrentDateTime, PDO::PARAM_STR);
            $stmt -> execute();

            while(true){
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);
                if($rec==false){
                    break;
                }
                
                //var_dump($reservation_data);
                $chack_date = (string)$rec['start_time_of_use'];
                //print $chack_date.'<br/>';
                $answer_date = strpos($chack_date,$search_date);
                //var_dump($answer_date);
                if($answer_date === false){
                    continue;
                    //$new_answer_date = 1;
                }
                if($answer_date === 0){
                    $new_answer_date = 0;
                }

            }
            $rec = null;
            if($new_answer_date==1){
                print'<p>予約がありません</p>';
            }    
            else{
            print '<form action="admin_reservation_edit_check.php" method="post">'; 
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
                $stmt -> execute();
                while(true){
                    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    if($rec==false){
                        break;
                    }
                    $chack_date = (string)$rec['start_time_of_use'];//
                    $answer_date = strpos($chack_date,$search_date);//


                    if($answer_date === 0){//
                    $reservation_number = $rec['reservation_number'];
                    $room_number = $rec['room_number'];
                    $sql2 = "SELECT room_name FROM facility_table WHERE room_number = ?";
                    //$sql2 = "SELECT room_name FROM category_table WHERE room_number = ?";
                    $stmt2 = $dbh->prepare($sql2);
                    $stmt2->execute([$room_number]);
                    $rec2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    print'<tr>';
                        print'<td><input type="radio" name="selected_reservation_number" value="'.$reservation_number.'"</td>';
                        //print'<td>'.$rec['user_Id'].'</td>';
                        print'<td>'.$rec['user_id'].'</td>';//学校用
                        print'<td>'.$rec2['room_name'].'</td>';
                        print'<td>'.$rec['number_of_user'].'</td>';
                        print'<td>'.$rec['start_time_of_use'].'</td>';
                        print'<td>'.$rec['end_time_of_use'].'</td>';
                        print'<td>'.$rec['sum_of_price'].'</td>';
                        print'<td>'.$rec['remark'].'</td>';
                    print'</tr>';
                    }//
                }
                $dbh = null;
                $stmt = null;
                $stmt2 = null;
                print'</table>';
                print'<input type="submit" value="選択した予約を変更する">';
                print'</form>';
            }
        }

        if($search_user_name == null && $search_date == null){
            print'<p>ユーザーIDか日付を入力して検索してください</p>';
                    
        } 
//</div>を追加
print '</div>';        
        print '<input type="button" onclick=location.href="admin_reservation_edit_list.php" value="予約状況一覧に戻る">';
        print '<input type="button" onclick=location.href="../../login/admin_home.php" value="ホーム画面に戻る">';
    ?>
    <script src="../../common/ebi.js"></script>
</body>
</html>