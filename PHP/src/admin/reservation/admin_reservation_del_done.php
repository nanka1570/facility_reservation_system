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
<title>予約のキャンセル完了</title> 
</head> 
<body> 
    <header class="admin-header">
        <h1>予約のキャンセル完了</h1>
    </header>

    <?php
        $reservation_number = $_POST['reservation_number'];
        $sql = "UPDATE reservation_table SET cancel_flag = 'C' WHERE reservation_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reservation_number]);

        $reservation_number = $_POST['reservation_number'];
        $sql = "UPDATE history_table SET cancel_flag = 'C' WHERE reservation_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reservation_number]);
        
    //<div class="content">を追加
    print '<div class="content">';

        print'<p>予約をキャンセルしました。</p>';

    //</div>を追加
    print '</div>';

        print'<form action="../../login/admin_home.php">';
            print'<input type="submit" value="ホーム画面に戻る">';
        print'</form>';
    ?>
</body>
</html>