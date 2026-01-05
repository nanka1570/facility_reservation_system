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

        print'<p>予約の変更を中止しました</p>';
        print'<form action="../../login/admin_home.php">';
            print'<input type="submit" value="ホーム画面に戻る" class="button"><br/>';
        print'</form>';
        print'<p>もう一度予約を変更する場合はこちら</p>';
        print'<form action="admin_reservation_edit_list.php">';
            print'<input type="submit" value="予約変更画面へ" class="button">';
        print'</form>';
    ?> 
</body>
</html>