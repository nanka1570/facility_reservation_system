<?php
//include_once "../common/session.php";
include_once "../../common/session.php";
$_SESSION['pagename']="利用予約";
include_once "../../login/user_home.php";
$_SESSION['pagename']="ホームページ";
?>
<!DOCTYPE html>
<html>
<head>
<meat charset="UTF-8">
<link rel="stylesheet" href="../../common/user_basic.css">
<link rel="stylesheet" href="reservation_top.css">
<title>予約トップ</title>

</head>
<body>

<table>
<tr>
<td><?php //<form action="reservation_add.php" method="post">?>
<form action="room.php" method="post">
    <input type="submit" class="top-button" value="新 規 予 約">
</form></td>

<!--<td><form action="reservation_list.php" method="post">-->
<td><form action="reservation_edit_list.php" method="post">
    <input type="submit" class="top-button" value="予 約 内 容 の 変 更">
    </form>
</td>

<td><form action="reservation_del_list.php" method="post">
    <input type="submit" class="top-button" value="予 約 の 削 除">
    </form>
</td>
</tr>
</table>
<br/>  
<!--<a href="../user_management/user_home.php">ホーム画面へ</a>-->
<a href="../../login/user_home.php">ホーム画面へ</a>
</body>
</html>