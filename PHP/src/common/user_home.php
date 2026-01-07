<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="../common/admin_basic.css">
<?php
//title,label
if(isset($_SESSION['pagename'])==false){
    $_SESSION['pagename']="ホームページ";
}
?>
<title><?php print $_SESSION['pagename'] ?></title>
<style>
.body_home{
    margin:0;
    padding: 0;
    overflow:scroll;
    background-color: beige;
}
.table_home{
    position: fixed;
    top: 00px;
    width: 100%;
    border-color: black;
    border-style: solid;
    border-collapse: collapse;
    z-index: 10;
}
.label_home{
    position: absolute;
    background-color: green;
    top: 30px;
    left: 30%;
    width: 40%;
    height: 35px;
    text-align: center;
    color: orangered;
    font-family: "HGP行書体";
    border-radius: 15px;
}
.user{
    position: absolute;
    top: 75px;
    left:5px;
    font-size:70%
}
.th_home{
    height: 100px;
    font-size: 150%;
    background-color: cadetblue;
}
.td_home{
    height: 40px;
    text-align:center ;
    font-family: "HGP創英ﾌﾟﾚｾﾞﾝｽEB";
    font-size: 130%;
    border-style: solid ;
    background-color: royalblue;
}
.a_home{
    color:whitesmoke;
    text-decoration:none;
}
a:hover{
    color: red;
}
.toppage{
    position: absolute;
    top :0;
    left:0;
    height: 25px;
    border-radius: 0;
    border-color: black;
}
.page_top{
    width: 40%;
    position: absolute;
    top:0;
    left: 30%;
    height: 25px;
    border-color: black;
}
.logout{
    position: absolute;
    top:0;
    right: 0;
    height: 25px;
    width: 80px;
    border-radius: 0;
    border-color: black;
}
.pagename{
    border-color: royalblue;
    background-color: royalblue;
    color: whitesmoke;
    font-size: 100%;
    font-family: "HGP創英ﾌﾟﾚｾﾞﾝｽEB";
    border-style: none;
}
</style>

</head>
<body class="body_home">
<?php
//include session
if(file_exists("../common/session.php")){
    include_once '../common/session.php';
}else if(file_exists("../../common/session.php")){
    include_once '../../common/session.php';
}else if(file_exists("../../common/session.php")){
    include_once '../../../common/session.php';
}
//トップページ(user_home)
if(file_exists("user_home.php")){
    $toppage='user_home.php';
}else if(file_exists("../login/user_home.php")){
    $toppage='../login/user_home.php';
}else if(file_exists("../../login/user_home.php")){
    $toppage='../../login/user_home.php';
}
//ログアウト
if(file_exists("logout_check.php")){
    $logout='logout_check.php';
}else if(file_exists("../login/logout_check.php")){
    $logout='../login/logout_check.php';
}else if(file_exists("../../login/logout_check.php")){
    $logout='../../login/logout_check.php';
}
//予約
if(file_exists("reservation_top.php")){
    $reservation='reservation_top.php';
}else if(file_exists("user/user_reservation/reservation_top.php")){
    $reservation='user/user_reservation/reservation_top.php';
}else if(file_exists("../user_reservation/reservation_top.php")){
    $reservation='../user_reservation/reservation_top.php';
}else if(file_exists("../user/user_reservation/reservation_top.php")){
    $reservation='../user/user_reservation/reservation_top.php';
}else if(file_exists("../../user/reservation_top.php")){
    $reservation='../../user/reservation_top.php';
}
//履歴
if(file_exists("controlq_user.php")){
    $inquily='controlq_user.php';
}else if(file_exists("../inquily/controlq_user.php")){
    $inquily='../inquily/controlq_user.php';
}else if(file_exists("../../inquily/controlq_user.php")){
    $inquily='../../inquily/controlq_user.php';
}
//問合せ
if(file_exists("rireki.php")){
    $history='rireki.php';
}else if(file_exists("../rireki/rireki.php")){
    $history='../rireki/rireki.php';
}else if(file_exists("../../rireki/rireki.php")){
    $history='../../rireki/rireki.php';
}else if(file_exists("../user/rireki/rireki.php")){
    $history='../user/rireki/rireki.php';
}else if(file_exists("../../user/rireki/rireki.php")){
    $history='../../user/rireki/rireki.php';
}
?>
    調整<br>調整<br>調整<br>調整<br>調整<br>調整<br><br>
    <table class="table_home">
        <colgroup>
            <col span="3" width=30%>
        </colgroup>
        <tr>
	    <th class="th_home" colspan="3">
            <p class="a_home" href="user_top.php">
                <label class="label_home"><?php print $_SESSION['pagename'] ?>🦐</label>
            </p>
            <label class="user"><?php print $_SESSION['user_name'] ?>さんログイン中</label>
            <input type="button" class="page_top" id="page_top" value="ページトップへ">
        <?php print'<form action="'.$logout.'" method="post"><input type="submit" class="logout" name="pagename" value="ログアウト"></form>'; ?>
        <?php print'<form action="'.$toppage.'" method="post"><input type="submit" class="toppage" name="pagename" value="トップページ"></form>'; ?>
        </th>
        </tr>
        <tr>
        <?php
        print'<form method="post" action="'.$reservation.'" ><td class="td_home" colspan="1">';
        print'<input type="submit" class="pagename" value="利用予約"></td></form>';
        print'<form method="post" action="'.$history.'"><td class="td_home" colspan="1">';
        print'<input type="submit" class="pagename" value="利用履歴"></td></form>';
        print'<form method="post" action="'.$inquily.'"><td class="td_home" colspan="1">';
        print'<input type="submit" class="pagename" value="問合せ"></td></form>';
        ?>
        </tr>
    </table>

<script>
// 変数pagetopの宣言
    let pagetop = document.getElementById("page_top");
// ページトップへ戻るボタンをクリックしたとき
    pagetop.addEventListener("click", () => {
    window.scroll({ top: 0, behavior: "smooth" });
    });
</script>
</body>
</html>