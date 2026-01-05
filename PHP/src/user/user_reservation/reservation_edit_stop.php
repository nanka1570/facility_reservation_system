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
        $reservation_number = $_POST['reservation_number'];
        $sql = "UPDATE reservation_table SET cancel_flag = '' WHERE reservation_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reservation_number]);

        //予約履歴も元に戻す
        $sql = "UPDATE history_table SET cancel_flag = '' WHERE reservation_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reservation_number]);       

        print'<p class="check">予約の変更を中止しました</p>';
        print'<form action="../../login/user_home.php">';
            print'<p class="buttons"><input type="submit" class="button" value="ホーム画面に戻る"></p>';
        print'</form>';
        print'<p class="check">もう一度予約を変更する場合はこちら</p>';
        print'<form action="reservation_edit_list.php">';
            print'<p class="buttons"><input type="submit" class="button" value="予約変更画面へ"></p>';
        print'</form>';
    ?> 
</body>
</html>