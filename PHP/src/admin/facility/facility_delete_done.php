<?php
include_once 'facility_common.php';

$success = false;
$message = '';

try {
    $room_number = $_POST['room_number'] ?? 0;
    $delete_items = $_POST['delete_items'] ?? [];
    $delete_items_list = $_POST['delete_items_list'] ?? [];

    // 削除前の情報を取得
    $data = getFacilityAndItemsForDelete($room_number, $delete_items_list);
    $facility_data = $data['facility'];
    $items_data = $data['items'];

    // 削除実行
    $result = executeDelete([
        'room_number' => $room_number,
        'delete_items' => $delete_items,
        'delete_items_list' => $delete_items_list
    ]);

    $success = $result['success'];
    $message = $result['message'];

} catch (Exception $e) {
    displayErrors([$e->getMessage()]);
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>削除完了</title>
    <link rel="stylesheet" href="admin_basic.css">
</head>
<body>
    <h1>削除完了</h1>

    <div class="<?= $success ? 'success-message' : 'error-message' ?>">
        <?= h($message) ?>
    </div>

    <?php if ($success): ?>
        <div class="delete-summary">
            <h2>削除された内容</h2>
            <?php if (in_array('facility', $delete_items)): ?>
                <div class="delete-item">
                    <h3>部屋全体を削除</h3>
                    <p>部屋番号: <?= h($facility_data['room_number']) ?></p>
                    <p>部屋名: <?= h($facility_data['room_name']) ?></p>
                    <p>分類情報: 
                        分類番号 <?= h($facility_data['current_category_number']) ?> - 
                        <?= h($facility_data['category_name']) ?>
                    </p>
                    <?php if (!empty($facility_data['max_number_of_people'])): ?>
                        <p>最大収容人数: <?= h($facility_data['max_number_of_people']) ?> 人</p>
                    <?php endif; ?>
                    <?php if (!empty($facility_data['equipment'])): ?>
                        <p>設備: <?= h($facility_data['equipment']) ?></p>
                    <?php endif; ?>
                    <?php if ($facility_data['time_of_unit_price'] > 0): ?>
                        <p>時間単位あたりの料金: <?= h($facility_data['time_of_unit_price']) ?> 円</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="delete-item">
                    <?php foreach ($delete_items as $item): ?>
                        <?php if ($item === 'category'): ?>
                            <p>分類情報: 
                                分類番号 <?= h($facility_data['current_category_number']) ?> - 
                                <?= h($facility_data['category_name']) ?>
                            </p>
                        <?php elseif ($item === 'equipment' && !empty($facility_data['equipment'])): ?>
                            <p>設備: <?= h($facility_data['equipment']) ?></p>
                        <?php elseif ($item === 'time_unit_price' && $facility_data['time_of_unit_price'] > 0): ?>
                            <p>時間単位あたりの料金: <?= h($facility_data['time_of_unit_price']) ?> 円</p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
            <?php endif; ?>

        
            <?php if (!empty($items_data)): ?>
                <h3>削除された備品</h3>
                <div class="delete-item">
                    <?php foreach ($items_data as $item): ?>
                        <p>備品番号 <?= h($item['item_number']) ?>:
                        <?= h($item['item_name']) ?>
                        (総数: <?= h($item['total_of_item']) ?>個
                        <?php if ($item['rental_unit_price'] > 0): ?>
                            、貸出単価: <?= h($item['rental_unit_price']) ?>円
                        <?php endif; ?>
                        )
                        </p>
                    <?php endforeach; ?>
                </div>        
            <?php endif; ?>
        <?php endif; ?>
    <div class="button-group">
        <input type="button" onclick="location.href='facility_edit_top.php'" value="施設一覧に戻る">
    </div>
</body>
</html>