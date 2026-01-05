<?php
//include_once "../common/session.php";
include_once "../../common/session.php";
if(isset($_POST['name']))
{
    $user_name=$_POST['name'];
}else{
    $user_name="";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <link rel="stylesheet" href="main.css">
        <h1>新規予約</h1>
    </head>
    <body>
        <form action="reservation_add_check.php" METHOD=POST>
            ユーザーID
            <input type="text" name="user_Id" value="<?php echo $_SESSION['user_Id'] ?>"><br />
            部屋番号
            <input type="text" name="room_number"><br />
            利用人数
            <input type="text" name="number_of_user"><br />
            利用開始時間
            <input type="text" name="start_use_year">年
            <input type="text" name="start_use_month">月
            <input type="text" name="start_use_day">日
            <input type="text" name="start_use_hour">時
            <input type="text" name="start_use_minite">分<br />

            利用終了時間
            <input type="text" name="end_use_year">年
            <input type="text" name="end_use_month">月
            <input type="text" name="end_use_day">日
            <input type="text" name="end_use_hour">時
            <input type="text" name="end_use_minite">分<br />

            備考
            <input type="text" name="remark"><br>
            <input type="submit" name=registration  value="予約">
        </form>
        <a href='reservation_top.php'>予約トップ画面へ</a>
    </body>
</html>