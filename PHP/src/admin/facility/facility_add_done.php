<?php
include_once 'facility_common.php';

try {
    $data = [
        'room_name' => $_POST['room_name'] ?? '',
        'category_name' => $_POST['category_name'] ?? '',
        'max_number_of_people' => $_POST['max_number_of_people'] ?? 0,
        'item_name' => $_POST['item_name'] ?? '',
        'total_of_item' => $_POST['total_of_item'] ?? 0,
        'equipment' => $_POST['equipment'] ?? '',
        'time_of_unit_price' => $_POST['time_of_unit_price'] ?? 0,
        'rental_unit_price' => $_POST['rental_unit_price'] ?? 0,
    ];

    // // データの内容を出力
    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";

    // 入力値のバリデーション
    $errors = validateFacilityInput($data);
    if (!empty($errors)) {
        displayErrors($errors);
        exit();
    }

    // 施設追加の実行
    $result = addFacility($data);

    if ($result['success']) {
        displaySuccess('以下の内容で登録しました：');
        
        // 登録内容の表示
        if(!empty($data['room_name'])) echo '部屋名: ' . h($data['room_name']) . '<br />';
        if(!empty($data['category_name'])) echo '分類名: ' . h($data['category_name']) . '<br />';
        if(!empty($data['max_number_of_people'])) echo '最大収容人数: ' . h($data['max_number_of_people']) . "人<br />";
        if(!empty($data['item_name'])) echo '備品名: ' . h($data['item_name']) . '<br />';
        if(!empty($data['total_of_item'])) echo '備品総数: ' . h($data['total_of_item']) . "個<br />";
        if(!empty($data['equipment'])) echo '設備: ' . h($data['equipment']) . '<br />';
        if(!empty($data['time_of_unit_price'])) echo '時間単位当たりの料金(部屋): ' . h($data['time_of_unit_price']) . "円<br />";
        if(!empty($data['rental_unit_price'])) echo '貸出単価(備品): ' . h($data['rental_unit_price']) . "円<br />";
    }

    }  catch (Exception $e) {
        displayErrors([$e->getMessage()]);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>施設追加完了</title>
        <meta name="facility_add_done" content="facility">
        <link rel="stylesheet" href="admin_basic.css">

    </head>
    <body>
        <div class="button-group">
            <input type="button" onclick="location.href='facility_edit_top.php'" value="施設一覧に戻る">
            <input type="button" onclick="location.href='facility_add.php'" value="追加ページに戻る">
        </div>
    </body>
</html>