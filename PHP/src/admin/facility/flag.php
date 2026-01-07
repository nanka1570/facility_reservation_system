<?php
// include_once '../DB/connect.php';
// include_once 'connect_myhouse.php';
include_once "../../common/DB_switch.php";


$dbh = $conn;
$dbh ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// 最新のフラグを取得
$sql="SELECT * 
      FROM extension_table 
      WHERE change_extension_date = (
      SELECT MAX(change_extension_date)
      FROM extension_table
      )";

$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// フラグの状態を変数に格納
// $use_extension_flag = ($result['use_extension'] ?? 'N') === 'Y';
// $use_extension_flag = $result['use_extension'] === 'Y';
// $rental_flag = $result['rental_flag'] === 'R';
// $price_flag = $result['price_flag'] === 'P';
// $equipment_flag = $result['equipment_flag'] === 'E';

// // display styleを決定する関数
// function getDisplayStyle($flag) {
//     return $flag ? 'block' : 'none';
//}

/////////////////////////////////////////////////////////////////////////////////////////////////
// フラグの状態を変数に格納(時間延長機能を追加)
$use_extension_flag = $result['use_extension'] === 'Y';
$rental_flag = $result['rental_flag'] === 'R';
$price_flag = $result['price_flag'] === 'P';
$equipment_flag = $result['equipment_flag'] === 'E';
$time_extension_flag = $result['time_extension_flag'] === 'T';
//display styleを決定する関数
function getDisplayStyle($flag) {
    return $flag ? 'block' : 'none';
}
/////////////////////////////////////////////////////////////////////////////////////////////////

?>