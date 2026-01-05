<?php
include_once 'facility_common.php';

$room_number = $_POST['room_number'] ?? 0;
$delete_items = $_POST['delete_items'] ?? [];
$delete_items_list = $_POST['delete_items_list'] ?? [];

try {
    $data = getFacilityAndItemsForDelete($room_number, $delete_items_list);
    $facility_data = $data['facility'];
    $items_data = $data['items'];
} catch (Exception $e) {
    displayErrors([$e->getMessage()]);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>削除内容の確認</title>
    <link rel="stylesheet" href="admin_basic.css">
</head>
<body>
    <h1>削除内容の確認</h1>

    <form method="post" action="facility_delete_done.php">
        <div class="confirm-content">
            <h2>以下の内容を削除します</h2>

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
                    <h3>削除する項目</h3>
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
                <div class="delete-item">
                    <h3>削除する備品</h3>
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
            <?php else: ?>
                削除に選択された備品はありませんでした。
            <?php endif; ?>

        <div class="warning">
            <p>※この操作は取り消せません。削除してよろしいですか？</p>
        </div>

        <div class="button-group">
            <input type="hidden" name="room_number" value="<?= h($room_number) ?>">
            <?php foreach ($delete_items as $item): ?>
                <input type="hidden" name="delete_items[]" value="<?= h($item) ?>">
            <?php endforeach; ?>
            <?php foreach ($delete_items_list as $item_id): ?>
                <input type="hidden" name="delete_items_list[]" value="<?= h($item_id) ?>">
            <?php endforeach; ?>
            <input type="button" onclick="history.back()" value="戻る">
            <input name="delete" type="submit" value="削除を実行">
        </div>
    </form>
</body>
</html>

