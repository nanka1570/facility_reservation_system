<!DOCTYPE html>
<html>
<head>
<?php
// if(file_exists("../common/user_basic.css")){
//     $css='../common/user_basic.css';
// }else if(file_exists("../../common/user_basic.css")){
//     $css='../../common/user_basic.css';
// }
?>
<link rel="stylesheet" href="<?php //print $css ?>">
<meta charset="UTF-8">
<?php
//title,label
if(isset($_SESSION['pagename'])==false){
    $_SESSION['pagename']="ホームページ";
}
?>
<title><?php print $_SESSION['pagename'] ?></title>

</head>
<body style='margin:0; padding: 0; overflow:scroll; background-color: beige;'>
<?php
//include session
if(file_exists("../common/session.php")){
    include_once '../common/session.php';
}else if(file_exists("../../common/session.php")){
    include_once '../../common/session.php';
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
    <table style='position: fixed; top: 00px; width: 100%;
                  border-color: black; border-style: solid; border-collapse: collapse;
                  z-index: 10;'>
        <colgroup>
            <col span="3" width=30%>
        </colgroup>
        <tr>
	    <th class="th_home" 
            style='
                height: 110px;
                font-size: 150%;
                background-color: cadetblue;'
            colspan="3">
            <p style='color:whitesmoke; text-decoration:none;' href="user_top.php">
                <label style='position: absolute;
                              background-color: green;
                              top: 45px; left: 30%;
                              width: 40%; height: 35px;
                              text-align: center; color: orangered; font-family: "HGP行書体"; font-weight: 700;
                              border-radius: 15px;'>
                <?php print $_SESSION['pagename'] ?>🦐</label>
            </p>
            <label style='position: absolute; top: 85px; left:5px; margin:0;
                          font-size:70%; font-weight:700; font-family: Helvetica Neue; color:#333;'>
                <?php print $_SESSION['user_name'] ?>さんログイン中</label>
            <input type="button" id="page_top"
                style='
                    position: absolute;
                    width: 40%; height: 35px;
                    color:black; font-size:18px; font-family: "游明朝"; font-weight: 500;
                    background-color: gold;
                    top:1px; left: 30%;
                    padding:0px;
                    border:2px; border-style:outset; border-color: black; border-radius: 3px;'
                value="ページトップへ">
        <?php print'<form action="'.$logout.'" method="post">'?>
        <input type="submit"
                style='
                    position: absolute;
                    color:black; font-size:18px; font-family: "游明朝"; font-weight: 500;
                    top:1px; right: 1px;
                    height: 35px; width: 100px;
                    background-color: tomato;
                    padding:0px;
                    border:2px; border-style:outset; border-color:red; border-radius: 3px;'
                value="ログアウト"></form>
        <?php print'<form action="'.$toppage.'" method="post">';?>
        <input type="submit" 
                style='
                    position: absolute;
                    color:black; font-size:18px; font-family: "游明朝"; font-weight: 500;
                    background-color: aqua;
                    top :1px; left:1px;
                    height: 35px; width:125px;
                    padding:0px;
                    border:2px; border-style:outset; border-color:blue; border-radius: 3px;'
                name="pagename" value="ホームページ"></form>
        </th>
        </tr>
        <tr>
<?php   print'<form method="post" action="'.$reservation.'" >';?>
        <td style='
                height: 40px;
                text-align:center ;
                font-family: "HGP創英ﾌﾟﾚｾﾞﾝｽEB";
                font-size: 130%;
                border-style: solid ;
                background-color: royalblue;' 
        colspan="1">
        <input type="submit" class="pagename" 
                style='
                    border-color: royalblue;
                    background-color: royalblue;
                    color: whitesmoke;
                    font-size: 100%; font-family: "HGP創英ﾌﾟﾚｾﾞﾝｽEB";
                    border-style: none;
                    padding:5px;'
                value="利用予約"></td></form>
<?php   print'<form method="post" action="'.$history.'">';?>
        <td style='
                height: 40px;
                text-align:center ;
                font-family: "HGP創英ﾌﾟﾚｾﾞﾝｽEB";
                font-size: 130%;
                border-style: solid ;
                background-color: royalblue;' 
        colspan="1">
        <input type="submit" class="pagename" 
                style='
                    border-color: royalblue;
                    background-color: royalblue;
                    color: whitesmoke;
                    font-size: 100%; font-family: "HGP創英ﾌﾟﾚｾﾞﾝｽEB";
                    border-style: none;
                    padding:5px;'
                value="利用履歴"></td></form>
<?php   print'<form method="post" action="'.$inquily.'">';?>
        <td style='
                height: 40px;
                text-align:center ;
                font-family: "HGP創英ﾌﾟﾚｾﾞﾝｽEB";
                font-size: 130%;
                border-style: solid ;
                background-color: royalblue;' 
        colspan="1">
        <input type="submit" class="pagename" 
                style='
                    border-color: royalblue;
                    background-color: royalblue;
                    color: whitesmoke;
                    font-size: 100%; font-family: "HGP創英ﾌﾟﾚｾﾞﾝｽEB";
                    border-style: none;
                    padding:5px;' 
                value="問合せ"></td></form>
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