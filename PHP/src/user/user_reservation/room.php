<?php
include "../../common/connect.php";
include "../../common/session.php";
// お家用
//include_once '../DB/connect_myhouse.php';
$_SESSION['pagename']="利用予約";
include_once "../../login/user_home.php";
$_SESSION['pagename']="ホームページ";
?>
<!DOCTYPE html>
<html>
<head>
<meat charset="UTF-8">
<link rel="stylesheet" href="room.css">
<title>部屋一覧</title>
</head>
<body>

<div class="top">
<p>カニ本舗　の予約ページへようこそ!</p>
<br>
</div>

<div class="t">
<p>プラン5件のうち1~3件を表示</p>
</div>


<?php

//ページネーションのために追加
    const RESULTS_PER_PAGE = 3;

    $page = max(1, (int)($_GET['page'] ?? 1));

    function getTotalResults($conn) {
        $sql = 'SELECT COUNT(*) as total FROM category_table';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // 総ページ数を計算
    $totalResults = getTotalResults($conn);
    $totalPages = ceil($totalResults / RESULTS_PER_PAGE);

    $offset = ($page - 1) * RESULTS_PER_PAGE;

//元のやつ
    // $room_cnt = 0;
    //         //$sql = "select distinct  category_name from facility_table where category_name=''";
    //         $sql = 'select  category_name ,category_number from category_table';
    //         $stmt = $conn->prepare($sql);
    //         $stmt->execute();
    //         $rec=$stmt->fetch();
    //             while($rec==true){
    //                  $data[]= $rec['category_name'];
    //             $rec=$stmt->fetch();
    //             $room_cnt=$room_cnt+1;
    //         }
    // $price_cnt = 0;
    //         $sql = 'select  time_of_unit_price from facility_table';
    //         $stmt = $conn->prepare($sql);
    //         $stmt->execute();
    //         $rec=$stmt->fetch();
    //             while($rec==true){
    //                  $unit[]= $rec['time_of_unit_price'];
    //             $rec=$stmt->fetch();
    //             $price_cnt=$price_cnt+1;
    //         }
    // $price=0;  

//ページネーション機能  
// カテゴリー情報  
$sql = 'select category_name, category_number 
        from category_table 
        ORDER BY category_number
        OFFSET :offset ROWS 
        FETCH NEXT :limit ROWS ONLY';
$stmt = $conn->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', RESULTS_PER_PAGE, PDO::PARAM_INT);
$stmt->execute();
$data = [];
while($rec = $stmt->fetch()) {
    $data[] = $rec['category_name'];
    $ctg[]=$rec['category_number'];//追加
}

// 料金情報
$sql = 'select time_of_unit_price 
        from facility_table 
        ORDER BY room_number
        OFFSET :offset ROWS 
        FETCH NEXT :limit ROWS ONLY';
$stmt = $conn->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', RESULTS_PER_PAGE, PDO::PARAM_INT);
$stmt->execute();
$unit = [];
while($rec = $stmt->fetch()) {
    $unit[] = $rec['time_of_unit_price'];
}

//元のやつ
//for($price=0; $price<$price_cnt ; $price++){
// for($room=0; $room<$room_cnt ; $room++){
for($room = 0; $room < min(count($data), RESULTS_PER_PAGE); $room++) {
    print'<div class="kaigi">';
    print'<div class="name">';
    print $data[$room];
    print'</div>';
    print'<table >';
    print'<tr>';
    ?> <td class="pop"><p class="te">推奨　4-20 人</p></td><?php
    ?><td class="akikaku"><p><form action="kaigi_aki.php">
    <!-- カーソル合わせたら指マークになるやつ(style="cursor: pointer;")を二か所に追加 -->
    <input type="submit" class="aki" style="cursor: pointer;" value="空き状況確認"></p> <?php
    print'</form></td>';
    print'</tr>';
    print'</table>';


    print'<table>';
    print'<tr>';
    print'<div class="price">';
    print'<td class="money"><p class="pritext">料金　';?> \<?php print $unit[$room];?> /h</p></td>
    <?php
    print '</td></div>';
    ?><!--<td class=yokaku><p><form action="kaigi_yoyaku.php">methodを追加-->
    <td class=yokaku><p><form method="post" action="reservation_add_01.php">
    <?php
    print '<input type="hidden" name="room_ctg" value='."{$ctg[$room]}".'>';
    ?>
    <input type="submit" class="yoyaku" style="cursor: pointer;" value="　予　約　"></td><?php
    print'</p></form></td>';
    print'</tr>';
    print'</table>';

    /*print'<div class="box">';
        print'<img src="kaigisitu.jfif" alt="会議室">';
    print'</div>';*/
    print'</div>';
    // $price++;
}
//}       
?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">前へ</a>
    <?php endif; ?>

    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">次へ</a>
    <?php endif; ?>
</div>

</body>
</html>