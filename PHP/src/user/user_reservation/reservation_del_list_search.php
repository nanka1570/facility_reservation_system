<?php 
include_once "../../common/connect.php"; 
include_once "../../common/session.php"; 
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
    //$search_user_name = $_SESSION['user_name'];
    $search_user_id = $_SESSION['user_Id'];//0117
    $search_date = $_POST['search_date'];
    $reservation_count = 0;
    $new_answer_date = 1;
    //var_dump($_SESSION);
    //var_dump($_POST);
    $dbh = $conn; 
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $currentDateTime = new DateTime();
    $formattedCurrentDateTime = $currentDateTime->format('Y-m-d H:i:s');

    $sql = "SELECT * 
            FROM reservation_table 
            WHERE start_time_of_use > :currentDateTime  AND user_Id= :search_user_Id AND cancel_flag='' ";//0117
            //WHERE start_time_of_use > :currentDateTime  AND user_Id= :search_user_name AND DATE_FORMAT(start_time_of_use, '%Y-%m-%d %H:%i:%s')  LIKE '%':search_date'%' AND cancel_flag='' ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':currentDateTime', $formattedCurrentDateTime, PDO::PARAM_STR);
            $stmt->bindParam(':search_user_Id', $search_user_id, PDO::PARAM_STR);//0117
            //$stmt->bindParam(':search_date', $search_date, PDO::PARAM_STR);
            $stmt -> execute();
            //var_dump($stmt);
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
                //var_dump($new_answer_date);print'</br>';
                //var_dump($rec);
                //print'<br/>';
                //$reservation_count++;

            //print $answer_date;
            //print $new_answer_date;

            if($new_answer_date==1){
                print '<br><br><br><br>';
                print'<p class="check">予約がありません</p>';
                print '<form>';
                    print '<p class="buttons"><input type="button" class="color" onclick=location.href="reservation_del_list.php" value="予約検索画面に戻る"></p>';
                print '</form>';
            }    
            else{
                print '<br><br><br>';
                print '<p class="title">予約状況</p>';
            print '<form action="reservation_del_check.php" method="post">'; 
                print'<table>';
                    print'<tr>';
                        print'<th scope="col" class="th_color"></th>';
                        print'<th scope="col"class="th_color">ユーザーID</th>';
                        print'<th scope="col" class="th_color">部屋名</th>';
                        print'<th scope="col" class="th_color">使用人数</th>';
                        print'<th scope="col" class="th_color">利用開始日時</th>';
                        print'<th scope="col" class="th_color">利用終了日時</th>';
                        print'<th scope="col" class="th_color">合計料金</th>';
                        print'<th scope="col" class="th_color">備考欄</th>';
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
                    }//
                }
                $dbh = null;
                $stmt = null;
                $stmt2 = null;
                print'</table>';
                print'<p class="buttons"><input type="submit" class="color" value="選択した予約をキャンセルする"></p>';
            print'</form>';

            print '<form>';
                print '<p class="buttons"><input type="button" class="color" onclick=location.href="reservation_del_list.php" value="一覧に戻る"></p>';
            print '</form>';

            print '<form>';
                print '<p class="buttons"><input type="button" class="color" onclick=location.href="../../login/user_home.php" value="ホーム画面に戻る"></p>';
            print '</form>';

            }
    ?>
</body>
<script src="../../common/ebi.js"></script>
</html>